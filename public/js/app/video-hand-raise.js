const video = document.getElementById('video');
const gallery = document.getElementById('gallery');
const identityInput = document.getElementById('identity');
const statusDiv = document.getElementById('status-message');

const joinRoomButton = document.getElementById('button-join');
const leaveRoomButton = document.getElementById('button-leave');
const raiseHandButton = document.getElementById('button-raise-hand');

const muteAllButton = document.getElementById('button-mute-all');

const ROOM_NAME = 'my-video-chat';
let videoRoom;
let localDataTrack;
let localVideoDiv;

const addLocalVideo = async () => {
    const videoTrack = await Twilio.Video.createLocalVideoTrack();
    localVideoDiv = document.createElement('div');
    localVideoDiv.classList.add('participant', 'localParticipant');

    const trackElement = videoTrack.attach();
    localVideoDiv.appendChild(trackElement);
    gallery.appendChild(localVideoDiv);
    leaveRoomButton.disabled = true;
};


const joinRoom = async (event) => {
    event.preventDefault();
    const identity = identityInput.value;
    identityInput.disabled = true;
    joinRoomButton.disabled = true;

    try {
        const response = await fetch('/api/room/token', {
            method: 'POST', headers: {
                'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken
            }, body: JSON.stringify({
                'identity': identity, 'roomName': ROOM_NAME
            })
        });

        const data = await response.json();

        // Creates the audio and video tracks
        localDataTrack = new Twilio.Video.LocalDataTrack();

        const localTracks = [...await Twilio.Video.createLocalTracks(), localDataTrack];

        videoRoom = await Twilio.Video.connect(data.token, {
            name: ROOM_NAME, tracks: localTracks
        });


        console.log(`You are now connected to Room ${videoRoom.name}`);

        const localParticipantDiv = document.getElementsByClassName('localParticipant participant')[0];
        localParticipantDiv.setAttribute('data-sid', videoRoom.localParticipant.sid);

        const identityDiv = document.createElement('div');
        identityDiv.classList.add('identity');
        identityDiv.innerHTML = identity;

        localParticipantDiv.appendChild(identityDiv);
        leaveRoomButton.disabled = false;
        raiseHandButton.disabled = false;
        muteAllButton.disabled = false;

        videoRoom.participants.forEach(participantConnected);
        videoRoom.on('participantConnected', participantConnected);
        videoRoom.on('participantDisconnected', participantDisconnected);

    } catch (error) {
        console.log(error);
    }
}

const leaveRoom = () => {
    if (videoRoom.localParticipant.state === 'connected') {
        videoRoom.disconnect();
    }

    statusDiv.innerText = `You are now disconnected from Room ${videoRoom.name}`;
    setTimeout(() => {
        statusDiv.innerText = ''
    }, 5000);

    // List all the participants
    let removeParticipants = gallery.getElementsByClassName('participant');

    // For remote participants, remove their entire div from the UI.
    // For the local participant, just remove their identity label from the UI
    // and return the UI back to how it looked before joining the call.

    for (participant of removeParticipants) {
        if (!participant.classList.contains('localParticipant')) {
            gallery.removeChild(participant);
        } else {
            const localParticipantDiv = document.getElementsByClassName('localParticipant participant')[0];
            const identity = localParticipantDiv.getElementsByClassName('identity');
            if (identity.length) {
                identity[0].remove();
            }
        }
    }

    joinRoomButton.disabled = false;
    leaveRoomButton.disabled = true;
    identityInput.disabled = false;
    raiseHandButton.disabled = true;
}

const participantConnected = (participant) => {
    console.log(`${participant.identity} has joined the call.`);

    // Add their video and audio to the gallery
    const participantDiv = document.createElement('div');
    participantDiv.setAttribute('data-sid', participant.sid);
    participantDiv.classList.add('participant');

    const tracksDiv = document.createElement('div');
    participantDiv.appendChild(tracksDiv);

    const identityDiv = document.createElement('div');
    identityDiv.classList.add('identity');
    identityDiv.innerHTML = participant.identity;
    participantDiv.appendChild(identityDiv);

    gallery.appendChild(participantDiv);

    participant.tracks.forEach(publication => {
        if (publication.isSubscribed) {
            tracksDiv.appendChild(publication.track.attach());
        }
    });

    participant.on('trackSubscribed', track => {
        if (track.kind === 'video' || track.kind === 'audio') {
            tracksDiv.appendChild(track.attach());
        }

        // Set up a listener for the data track
        if (track.kind === 'data') {

            track.on('message', message => {

                const messageData = JSON.parse(message);
                const raisedHandSid = messageData.sid;
                const selectedVideoDiv = document.querySelector(`div[data-sid=${raisedHandSid}]`);

                console.log('message' + message);
                if (messageData.muted == true) {

                    alert('you are muted');
                }

                /*  if (messageData.muted) {
                      selectedVideoDiv.classList.add('raised');
                  }*/
                // Add or remove the raised class to show or dismiss the raised hand
                console.log(messageData.raised);
                if (messageData.raised) {

                    this.processIncomingMessage(messageData.raised);

                    selectedVideoDiv.classList.add('raised');
                } else {

                    if (selectedVideoDiv) {
                        selectedVideoDiv.classList.remove('raised');
                    } else {
                        console.error('selectedVideoDiv not found');
                    }

                }
            });
        }
    });

    participant.on('trackUnsubscribed', track => {
        // Remove audio and video elements from the page
        if (track.kind === 'audio' || track.kind === 'video') {
            track.detach().forEach(element => element.remove());
        }
    });
};

function processIncomingMessage(message, participant) {
    const messageData = JSON.parse(message);
    switch (messageData.type) {
        case 'handStatus':
            // Aktualisieren Sie hier den Status der gehobenen Hand
            break;
        case 'requestHandStatus':
            // Senden Sie Ihren aktuellen Handstatus an den anfragenden Teilnehmer
            break;
        // Weitere Fälle können hier hinzugefügt werden
    }
}


const participantDisconnected = (participant) => {
    const participants = Array.from(document.getElementsByClassName('participant'));
    participants.find(div => div.dataset.sid === participant.sid).remove();
};

const handIsRaised = () => {
    if (raiseHandButton.classList.contains('raised') && localVideoDiv.classList.contains('raised')) {
        return true;
    }
    return false;
}

const raiseHand = async () => {
    // Show a raised hand in the UI for the local participant, then
    // send the raised hand notification via data track to the others on the call
    raiseHandButton.classList.add('raised');
    localVideoDiv.classList.add('raised');

    localDataTrack.send(JSON.stringify({
        sid: videoRoom.localParticipant.sid, raised: true
    }));
}

const lowerHand = async () => {
    raiseHandButton.classList.remove('raised');
    localVideoDiv.classList.remove('raised');

    localDataTrack.send(JSON.stringify({
        sid: videoRoom.localParticipant.sid, raised: false
    }));
}

const MuteAll = async () => {

    localDataTrack.send(JSON.stringify({
        participants: videoRoom.localParticipant.sid, muted: 1
    }));
}
// Show the participant a preview of their video
addLocalVideo();

// Event listeners
// Event listeners
joinRoomButton.addEventListener('click', joinRoom);
leaveRoomButton.addEventListener('click', leaveRoom);

raiseHandButton.addEventListener('click', () => {
    handIsRaised() ? lowerHand() : raiseHand();
});

muteAllButton.addEventListener('click', () => {

    MuteAll();
});

