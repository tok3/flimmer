<?php

namespace App\Http\Controllers;

use App\Events\HandRaiseEvent;
use App\Events\StagingToggleEvent;
use App\Events\TestEvent;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Illuminate\Support\Facades\Log;
use Auth;
use App\Events\MuteToggleRemoteEvent;
use App\Events\MuteAllEvent;
use Illuminate\Support\Facades\Cache;

/**
 *
 */
class VideoRoomsController extends Controller
{
    /**
     * Twilio configuration values
     */
    protected $sid;
    protected $token;
    protected $key;
    protected $secret;

    /**
     * Initialize the controller instance and set Twilio configurations.
     */
    public function __construct()
    {
        $this->sid = config('services.twilio.sid');
        $this->token = config('services.twilio.token');
        $this->key = config('services.twilio.key');
        $this->secret = config('services.twilio.secret');
    }

    /**
     * Display the main index page with a list of available rooms.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $rooms = [];
        try
        {
            $client = new Client($this->sid, $this->token);
            $allRooms = $client->video->v1->rooms->read([]);

            $rooms = array_map(function ($room) {
                return $room->uniqueName;
            }, $allRooms);

        }
        catch (\Exception $e)
        {
            Log::error("Error fetching rooms: " . $e->getMessage());
        }

        return view('index', ['rooms' => $rooms]);
    }


    public function generateToken(Request $request)
    {

        //$identity = "user_" . rand(1000, 9999);
        $identity = $request->input('identity');

        $token = new AccessToken($this->sid, $this->key, $this->secret, 3600, $identity);
        $videoGrant = new VideoGrant();
        $videoGrant->setRoom($request->roomName);
        $token->addGrant($videoGrant);

        return response()->json(['token' => $token->toJWT()]);
    }


    /**
     * Create a new video room.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createRoom(Request $request)
    {
        $client = new Client($this->sid, $this->token);

        $exists = $client->video->v1->rooms->read(['uniqueName' => $request->roomName]);

        if (empty($exists))
        {
            $room = $client->video->v1->rooms->create([
                'uniqueName' => $request->roomName,
                'type' => 'group',
                'recordParticipantsOnConnect' => false
            ]);


            return response()->json(['room' => $room->sid]);


            Log::debug("Created new room: " . $request->roomName);

            return response()->json(['success' => true, 'message' => 'Room created successfully']);
        }
        else
        {

            return response()->json(['success' => false, 'message' => 'Room already exists']);
        }

    }


    public function listRooms()
    {

        $client = new Client($this->sid, $this->token);
        $allRooms = $client->video->v1->rooms->read([]);
        $rooms = array_map(function ($room) {
            return $room->uniqueName;
        }, $allRooms);

        return response()->json($rooms);
    }


    /**
     * Join a specific video room.
     *
     * @param string $roomName
     * @return \Illuminate\View\View
     */
    public function joinRoom(string $roomName)
    {
        // Eindeutiger Bezeichner für diesen Benutzer
        $identity = \Auth::user()->name;

        Log::debug("Joined with identity: $identity");

        $token = new AccessToken($this->sid, $this->key, $this->secret, 3600, $identity);

        $videoGrant = new VideoGrant();
        $videoGrant->setRoom($roomName);

        $token->addGrant($videoGrant);

        return view('room', [
            'accessToken' => $token->toJWT(),
            'roomName' => $roomName
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function triggerMuteAllEvent(Request $request)
    {

        $data = $request->input('participants');

        event(new MuteAllEvent($data));

        return response()->json(['message' => 'Event ausgelöst']);
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function triggerToggleMuteEvent(Request $request)
    {

        $data = $request->input('participants');

        event(new MuteToggleRemoteEvent($data));

        return response()->json(['message' => 'Event ausgelöst']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function triggerHandRaiseEvent(Request $request)
    {
        $participant_id = $request->input('participant');
        $room_id = $request->input('roomId');


       //$cached =  $this->setHandraiseToggleCache($room_id, $participant_id);
       $cached =  $this->setToggleCache($room_id, $participant_id,'raised');

       // event(new HandRaiseEvent($participant_id));
        event(new HandRaiseEvent ($cached));

        return response()->json(['message' => 'Event ausgelöst']);
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cleanHandRaiseEvent(Request $request)
    {
        $participant_id = $request->input('participant');
        $room_id = $request->input('roomId');

        $cacheKey = 'raised_' . $room_id;

        $raised = Cache::get($cacheKey);

        unset($raised[$participant_id]);
        Cache::put('raised_' . $room_id, $raised, now()->addMinutes(30));

        event(new HandRaiseEvent ([$participant_id=>'false']));

        return response()->json(['message' => 'Event ausgelöst']);
    }

    /**
     * Set the handraise toggle cache for a given room and participant.
     *
     * @param int $_room_id The ID of the room.
     * @param int $_participant_id The ID of the participant.
     * @return mixed The updated handraise toggle cache for the room.
     */
    private function setHandraiseToggleCache($_room_id, $_participant_id)
    {

        $cacheKey = 'raised_' . $_room_id;

        if (isset(Cache::get($cacheKey)[$_participant_id]))
        {
            $raised = Cache::get($cacheKey);

            unset($raised[$_participant_id]);
            Cache::put($cacheKey, $raised, now()->addMinutes(30));


        }
        else
        {

            $raised = Cache::get($cacheKey);

            if (!is_array($raised))
            {
                $raised = [];
            }

            $raised[$_participant_id] = true;

            Cache::put($cacheKey, $raised, now()->addMinutes(30));
        }


        return Cache::get($cacheKey);

    }

    //------------------------------------------------------------------------------------


    public function triggerStagingEvent(Request $request)
    {
        $participant_id = $request->input('participant');
        $room_id = $request->input('roomId');


        $cached =  $this->setToggleCache($room_id, $participant_id,'staged');

        event(new StagingToggleEvent($cached));

        return response()->json(['message' => 'Staging Event ausgelöst']);
    }



    private function setToggleCache($_room_id, $_participant_id,$_keyPreFx = '')
    {

        $cacheKey = $_keyPreFx .'_'. $_room_id;

        if (isset(Cache::get($cacheKey)[$_participant_id]))
        {
            $enabled = Cache::get($cacheKey);

            unset($enabled[$_participant_id]);
            Cache::put($cacheKey, $enabled, now()->addMinutes(30));


        }
        else
        {

            $enabled = Cache::get($cacheKey);

            if (!is_array($enabled))
            {
                $enabled = [];
            }

            $enabled[$_participant_id] = true;

            Cache::put($cacheKey, $enabled, now()->addMinutes(30));
        }


        return Cache::get($cacheKey);

    }


    public function cleanToggleCache(Request $request)
    {
        $participant_id = $request->input('participant');
        $room_id = $request->input('roomId');
        $keyPreFx = $request->input('keyPreFx');


        $cacheKey = $keyPreFx. '_' . $room_id;

        $enabled = Cache::get($cacheKey);

        unset($enabled[$participant_id]);
        Cache::put($keyPreFx. '_' . $room_id, $enabled, now()->addMinutes(30));

        event(new HandRaiseEvent ([$participant_id=>'false']));

        return response()->json(['message' => 'Event ausgelöst']);
    }

    /*
        function testCache()
        {

            $_room_id = 'RMb519a1edb463c3a41f9eac86ca50dc66';
            $_participant_id = 'PA6659108d4fa680abab7f0e97f615a326';

            Cache::pull('raised_' . $_room_id);
            if (isset(Cache::get('raised_' . $_room_id)[$_participant_id]))
            {
                $raised = Cache::get('raised_' . $_room_id);

                unset($raised[$_participant_id]);
                Cache::put('raised_' . $_room_id, $raised, now()->addMinutes(30));


            }

        }*/
/*
    // Status setzen
    public function updateHandStatus(Request $request)
    {
        $participantId = $request->participant_id;
        $handRaised = $request->hand_raised;
        Cache::put('hand_status_' . $participantId, $handRaised, now()->addMinutes(30));

        return response()->json(['message' => 'Hand status updated']);
    }
*/
    // Status abrufen
    public function getHandStatuses($roomId)
    {
        $raised = Cache::get('raised_' . $roomId, []);

        return response()->json($raised);
    }


}
