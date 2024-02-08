<?php

namespace App\Http\Controllers;

use App\Events\TestEvent;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Illuminate\Support\Facades\Log;
use Auth;
use App\Events\MuteAllParticipants;
use App\Events\MuteAllEvent;

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



    public function triggerMuteAllEvent(Request $request)
    {
       /* $data = [
            'message' => 'Alle Teilnehmer wurden stummgeschaltet',
            'timestamp' => now(),
            // ... andere Daten, die Sie senden möchten ...
        ];*/

        $data = $request->input('participants');

        event(new MuteAllEvent($data));
        return response()->json(['message' => 'Event ausgelöst']);
    }

    public function muteParticipant(Request $request)
    {
        $participantSid = $request->input('participantSid');
        $roomName = $request->input('roomName');

/*
        // Erstellen Sie ein AccessToken mit DataTrackGrant
        $token = new AccessToken(
            $this->sid,
            $this->key,
            $this->secret,
            3600,
            $participantSid
        );

        $dataTrackGrant = new DataTrackGrant();
        $token->addGrant($dataTrackGrant);*/

        // Senden Sie eine benutzerdefinierte "mute"-Nachricht an den Client
        // Hier müssen Sie Ihre eigene Logik implementieren, um die Nachricht über den DataTrack zu senden

        return response()->json(['success' => true]);
    }


}
