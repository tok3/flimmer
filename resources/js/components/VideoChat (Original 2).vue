<template>
    <div>
        <div>
            <button @click="triggerMuteAll">Mute All</button>
        </div>
        <div class="ml-10 mt-10 mr-10">
            <div class="grid grid-cols-3 gap-4 mb-10 ">
                <div class="col-span-1">
                    <form v-if="!isConnected" @submit.prevent="submitForm">
                        <select v-model="selectedRoom" id="selectedRoom" class="px-4 py-2 w-full">
                            <option v-if="rooms.length" disabled value="">Raum wählen</option>
                            <option v-for="room in rooms" :key="room" :value="room">
                                {{ room }}
                            </option>
                        </select>
                        <div class="mb-4">
                            <label for="roomName" class="blocktext-sm font-medium text-gray-700">Raumname</label>
                            <input type="text" v-model="roomName" id="roomName" name="roomName" class="mt-1 p-2 w-full border rounded-md" placeholder="Geben Sie einen Raumnamen ein">

                            <input v-model="username" id="username" class="mt-1 p-2 w-full border rounded-md" placeholder="Geben Sie Ihren Benutzernamen ein">

                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Raum erstellen</button>
                    </form>
                    <button v-else @click="disconnectFromRoom" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Disconnect</button>
                    <button @click="toggleMute" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Stummschalten/Umschalten</button>
                    <button v-if="isConnected && isAdmin" @click="muteAll" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Alle stummschalten</button>

                </div>


                <!--                <div class="inline-flex rounded-md shadow-sm" role="group">
                                    <button type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-l-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                                        Profile
                                    </button>
                                    <button type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                                        Settings
                                    </button>
                                    <button type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-r-md hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                                        Messages
                                    </button>
                                </div>-->

            </div>


            <!-- Hier können Sie ein Formular zum Erstellen eines Raums und zum Anzeigen des Videos hinzufügen -->
            <div id="local-video" class="video-container">

                <!-- Lokales Video hier -->
            </div>
            <div id="remote-videos" class="video-container">
                <!-- Entfernte Videos hier -->
            </div>
        </div>

    </div>
    <svg width="24px" height="24px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
        <path fill="#ffffff"
              d="m412.16 592.128-45.44 45.44A191.232 191.232 0 0 1 320 512V256a192 192 0 1 1 384 0v44.352l-64 64V256a128 128 0 1 0-256 0v256c0 30.336 10.56 58.24 28.16 80.128zm51.968 38.592A128 128 0 0 0 640 512v-57.152l64-64V512a192 192 0 0 1-287.68 166.528l47.808-47.808zM314.88 779.968l46.144-46.08A222.976 222.976 0 0 0 480 768h64a224 224 0 0 0 224-224v-32a32 32 0 1 1 64 0v32a288 288 0 0 1-288 288v64h64a32 32 0 1 1 0 64H416a32 32 0 1 1 0-64h64v-64c-61.44 0-118.4-19.2-165.12-52.032zM266.752 737.6A286.976 286.976 0 0 1 192 544v-32a32 32 0 0 1 64 0v32c0 56.832 21.184 108.8 56.064 148.288L266.752 737.6z"/>
        <path fill="#ffffff" d="M150.72 859.072a32 32 0 0 1-45.44-45.056l704-708.544a32 32 0 0 1 45.44 45.056l-704 708.544z"/>
    </svg>


</template>


<style>
.video-container {
    width: 100%;
    height: 400px;
    background-color: #e2e2e2;
    position: relative;
}

#remote-videos {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
}

#local-video {
    width: 100px; /* oder die gewünschte Größe */
    height: 75px; /* oder die gewünschte Größe */
    object-fit: cover;
    margin: 10px;
}

.participant-container {
    width: 100%;
    height: 150px;
    position: relative;
}

.participant-container video {
    width: 100%;

    object-fit: cover;
}

.participant-label {
    display: flex;
    align-items: center;
    white-space: nowrap;
    position: absolute;
    bottom: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    padding: 5px 10px;
    z-index: 1;
}

.local-mute-icon {
    /* Ihr Stil für das lokale Mute-Icon */
    position: absolute;
    top: -5px;
    right: -5px;
    width: 24px;
    height: 24px;
    z-index: 2;
}


/* Beispiel für ein Responsive Design */
/*
@media (max-width: 600px) {
    #remote-videos video {
        max-width: 100%;
    }
}
*/


</style>
<script>
import axios from 'axios';
import { connect } from 'twilio-video';

let currentRoom = null;
let icoLocalMuted = '<svg width="24px" height="24px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><path fill="#ff0000" d="m412.16 592.128-45.44 45.44A191.232 191.232 0 0 1 320 512V256a192 192 0 1 1 384 0v44.352l-64 64V256a128 128 0 1 0-256 0v256c0 30.336 10.56 58.24 28.16 80.128zm51.968 38.592A128 128 0 0 0 640 512v-57.152l64-64V512a192 192 0 0 1-287.68 166.528l47.808-47.808zM314.88 779.968l46.144-46.08A222.976 222.976 0 0 0 480 768h64a224 224 0 0 0 224-224v-32a32 32 0 1 1 64 0v32a288 288 0 0 1-288 288v64h64a32 32 0 1 1 0 64H416a32 32 0 1 1 0-64h64v-64c-61.44 0-118.4-19.2-165.12-52.032zM266.752 737.6A286.976 286.976 0 0 1 192 544v-32a32 32 0 0 1 64 0v32c0 56.832 21.184 108.8 56.064 148.288L266.752 737.6z"/><path fill="#ff0000" d="M150.72 859.072a32 32 0 0 1-45.44-45.056l704-708.544a32 32 0 0 1 45.44 45.056l-704 708.544z"/></svg>';

let icoRemoteMuted = '<svg width="24px" height="24px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><path fill="#ffffff" d="m412.16 592.128-45.44 45.44A191.232 191.232 0 0 1 320 512V256a192 192 0 1 1 384 0v44.352l-64 64V256a128 128 0 1 0-256 0v256c0 30.336 10.56 58.24 28.16 80.128zm51.968 38.592A128 128 0 0 0 640 512v-57.152l64-64V512a192 192 0 0 1-287.68 166.528l47.808-47.808zM314.88 779.968l46.144-46.08A222.976 222.976 0 0 0 480 768h64a224 224 0 0 0 224-224v-32a32 32 0 1 1 64 0v32a288 288 0 0 1-288 288v64h64a32 32 0 1 1 0 64H416a32 32 0 1 1 0-64h64v-64c-61.44 0-118.4-19.2-165.12-52.032zM266.752 737.6A286.976 286.976 0 0 1 192 544v-32a32 32 0 0 1 64 0v32c0 56.832 21.184 108.8 56.064 148.288L266.752 737.6z"/><path fill="#ffffff" d="M150.72 859.072a32 32 0 0 1-45.44-45.056l704-708.544a32 32 0 0 1 45.44 45.056l-704 708.544z"/></svg>';

export default {
    data() {
        return {
            roomName: '', // für das Textfeld
            selectedRoom: '', // für das Dropdown-Menü
            accessToken: '',
            rooms: [],
            isConnected: false,
            username: `FakeUsername${Math.floor(Math.random() * 10000)}`,
            showMuteIcon: false,
            isAdmin: true,
            remoteParticipants: [],
            icoRemoteMuted:icoRemoteMuted,

        };
    },
    methods: {

        async createRoom() {
            try {
                const response = await axios.post('/api/room/create', {roomName: this.roomName});
                if (response.data.room) {
                    // Raum erfolgreich erstellt
                    this.joinRoom();
                }
            } catch (error) {
                console.error('Error creating room:', error);
            }
        },

        // Hilfsfunktion zum Anhängen von Tracks
        attachTracks(participant) {
            participant.on('trackSubscribed', track => {
                document.getElementById('remote-videos').appendChild(track.attach());
            });
        },

        // Hilfsfunktion zum Entfernen von Tracks
        detachParticipantTracks(participant) {
            // Entfernen des gesamten Containers des Teilnehmers
            const participantContainer = document.getElementById(`participant-${participant.sid}`);
            if (participantContainer) {
                participantContainer.remove();
            }
        },
        addMuteIcon(participantSid) {
            const participantContainer = document.getElementById(`participant-${participantSid}`);
            if (participantContainer) {
                const label = participantContainer.querySelector('.participant-label');
                const muteIcon = document.createElement('span');
                muteIcon.innerHTML = icoRemoteMuted; // Ihr SVG-Icon hier
                muteIcon.classList.add('mute-icon');
                label.appendChild(muteIcon);
            } else {
                console.warn(`Teilnehmer-Container für ${participantSid} nicht gefunden.`);
            }
        },
        removeMuteIcon(participantSid) {
            const participantContainer = document.getElementById(`participant-${participantSid}`);
            if (participantContainer) {
                const muteIcon = participantContainer.querySelector('.mute-icon');
                if (muteIcon) {
                    muteIcon.remove();
                }
            } else {
                console.warn(`Teilnehmer-Container für ${participantSid} nicht gefunden.`);
            }
        },
        // Hilfsfunktion zum Anhängen von Tracks und Hinzufügen des Labels
        attachParticipantTracks(participant) {

            // Erstellen Sie ein neues Container-Element für den Teilnehmer
            const participantContainer = document.createElement('div');
            participantContainer.id = `participant-${participant.sid}`;
            participantContainer.classList.add('participant-container');


            // Fügen Sie ein Label für den Benutzernamen hinzu
            const label = document.createElement('div');
            label.innerText = participant.identity;
            label.classList.add('participant-label');
            participantContainer.appendChild(label);



            // Überprüfen Sie den aktuellen Mute-Status und fügen Sie das Mute-Icon hinzu, falls erforderlich
            const audioTrackPublication = participant.audioTracks.values().next().value;
            if (audioTrackPublication && audioTrackPublication.track) {
                if (!audioTrackPublication.track.isEnabled) {
                    this.addMuteIcon(participant.sid);
                }
            } else {
                console.warn("AudioTrackPublication oder AudioTrack fehlt für den Teilnehmer:", participant.identity);
            }


            // Anhängen von Video- und Audio-Tracks
            participant.tracks.forEach(publication => {
                if (publication.isSubscribed) {
                    const track = publication.track;
                    const trackElement = track.attach();
                    trackElement.classList.add('participant-track');
                    participantContainer.appendChild(trackElement);

                    // Überprüfen Sie den Mute-Status und zeigen Sie das Mute-Icon an, wenn der Audio-Track deaktiviert ist
                    if (track.kind === 'audio' && !track.isEnabled) {
                        this.addMuteIcon(participant.sid);
                    }
                }
            });

            // Event-Handler für das Abonnieren von Tracks
            participant.on('trackSubscribed', track => {
                const trackElement = track.attach();
                trackElement.classList.add('participant-track');
                participantContainer.appendChild(trackElement);

                // Überprüfen Sie den Mute-Status und zeigen Sie das Mute-Icon an, wenn der Audio-Track deaktiviert ist
                if (track.kind === 'audio' && !track.isEnabled) {
                    this.addMuteIcon(participant.sid);
                }
            });


            // Event-Handler für das Deaktivieren und Aktivieren von Tracks
            participant.on('trackDisabled', track => {
                if (track.kind === 'audio') {
                    this.addMuteIcon(participant.sid);
                }
            });

            participant.on('trackEnabled', track => {
                if (track.kind === 'audio') {
                    this.removeMuteIcon(participant.sid);
                }
            });


            document.getElementById('remote-videos').appendChild(participantContainer);
        },
        // Funktion zum Hinzufügen des Mute-Icons zum lokalen Video
        addLocalMuteIcon() {
            const localVideoContainer = document.getElementById('local-video');
            const muteIcon = document.createElement('span');
            muteIcon.innerHTML = icoLocalMuted; // Ihr SVG-Icon hier
            muteIcon.classList.add('local-mute-icon');
            localVideoContainer.appendChild(muteIcon);
        },

        // Funktion zum Entfernen des Mute-Icons vom lokalen Video
        removeLocalMuteIcon() {
            const localVideoContainer = document.getElementById('local-video');
            const muteIcon = localVideoContainer.querySelector('.local-mute-icon');
            if (muteIcon) muteIcon.remove();
        },

        toggleMute() {
            const localAudioTrack = Array.from(currentRoom.localParticipant.audioTracks.values())[0].track;
            if (localAudioTrack.isEnabled) {
                localAudioTrack.disable();
                this.addLocalMuteIcon();
            } else {
                localAudioTrack.enable();
                this.removeLocalMuteIcon();
            }
        },
        async joinRoom() {
            try {
                const tokenResponse = await axios.post('/api/room/token', {roomName: this.roomName, identity: this.username});
                this.accessToken = tokenResponse.data.token;
                const room = await connect(this.accessToken, {name: this.roomName});
                currentRoom = room;

                // Speichern Sie die sid des lokalen Teilnehmers
                this.localParticipantSid = room.localParticipant.sid;

                this.isConnected = true;


                // Lokales Video anhängen
                const localVideoContainer = document.getElementById('local-video');
                const localTracks = Array.from(room.localParticipant.tracks.values());
                localTracks.forEach(publication => {
                    if (publication.track && typeof publication.track.attach === 'function') {
                        localVideoContainer.appendChild(publication.track.attach());
                    }
                });

                room.on('participantConnected', participant => {
                    console.log(`Teilnehmer "${participant.identity}" verbunden`);
                    this.attachParticipantTracks(participant);
                    this.onParticipantConnected(participant);
                });

                room.on('participantDisconnected', participant => {
                    console.log(`Teilnehmer "${participant.identity}" getrennt`);
                    this.detachParticipantTracks(participant);
                    this.onParticipantDisconnected(participant);
                });

                // Bereits vorhandene Teilnehmer behandeln und anzeigen
                room.participants.forEach(participant => {
                    console.log(`Bereits im Raum: "${participant.identity}"`);
                    this.attachParticipantTracks(participant);
                    this.onParticipantConnected(participant);
                });

                // Hören Sie auf Nachrichten auf dem DataTrack
                room.on('dataTrackSubscribed', (dataTrack) => {
                    dataTrack.on('message', (message) => {
                        const data = JSON.parse(message);
                        if (data.action === 'mute' && data.participantSid !== room.localParticipant.sid) {
                            // Schalten Sie das Mikrofon stumm und zeigen Sie das Stummschalt-Symbol an
                            this.toggleMute();
                        }
                    });
                });

            } catch (error) {
                console.error('Error joining room:', error);
            }
        },
        async fetchRooms() {
            try {
                const response = await axios.get('/api/rooms');

                console.log(this.rooms);

                this.rooms = response.data;
            } catch (error) {
                console.error('Error fetching rooms:', error);
            }
        },
        async submitForm() {
            if (this.selectedRoom) {
                this.roomName = this.selectedRoom; // Setzen Sie roomName auf den ausgewählten Raum
                this.joinRoom();
                this.selectedRoom = '';

            } else if (this.roomName.trim() !== '') {
                await this.createRoom();
            } else {
                console.error('Bitte wählen Sie einen Raum aus dem Dropdown-Menü oder geben Sie einen neuen Raumnamen ein.');
            }
        },
        disconnectFromRoom() {
            if (currentRoom) {
                // 1. Alle lokalen Tracks stoppen und entfernen
                currentRoom.localParticipant.tracks.forEach(publication => {
                    publication.track.stop();
                    const attachedElements = publication.track.detach();
                    attachedElements.forEach(element => element.remove());
                });

                // 2. Alle entfernten Teilnehmer-Tracks entfernen
                currentRoom.participants.forEach(participant => {
                    this.detachParticipantTracks(participant);
                });

                // 3. Entfernen Sie das Mute-Icon vom lokalen Video
                const localVideoContainer = document.getElementById('local-video');
                const muteIcon = localVideoContainer.querySelector('.local-mute-icon');
                if (muteIcon) muteIcon.remove();

                // 4. Entfernen Sie alle Kinder von local-video, lassen Sie aber den Container selbst intakt
                while (localVideoContainer.firstChild) {
                    localVideoContainer.firstChild.remove();
                }

                // 5. Trennen Sie die Verbindung zum Raum und setzen Sie die currentRoom Variable zurück
                currentRoom.disconnect();
                currentRoom = null;

                // 6. Setzen Sie den isConnected Status zurück
                this.isConnected = false;
            }
        },
        muteParticipant(participantSid) {

            const localAudioTrack = Array.from(currentRoom.localParticipant.audioTracks.values())[0].track;
            if ((participantSid === this.localParticipantSid) && localAudioTrack.isEnabled) {
                localAudioTrack.disable();
                this.addLocalMuteIcon();

            }

        },
        muteAllParticipants(participants) {
            console.log('participants: ' + participants)

            participants.forEach(participant => {

                this.muteParticipant(participant.sid);

            });
        },
        onParticipantConnected(participant) {
            this.remoteParticipants.push({
                identity: participant.identity,
                sid: participant.sid
            });
        },
        onParticipantDisconnected(participant) {
            const index = this.remoteParticipants.indexOf(participant);
            if (index !== -1) {
                this.remoteParticipants.splice(index, 1);
            }
        },
        async muteAll() {
            try {
                const response = await axios.post('/trigger-mute-all', {
                    participants: this.remoteParticipants
                });

                console.log('MuteAll Event ausgelöst:', response.data.message);
            } catch (error) {
                console.error('Fehler beim Auslösen des MuteAll-Events:', error);
            }
        }
    },
    mounted() {
        this.fetchRooms();
        this.$nextTick(() => {
            new Masonry('#remote-videos', {
                itemSelector: '.participant-container',
                columnWidth: 200,
                gutter: 10
            });


            window.Echo.channel('mute-all-channel')
                .listen('.mute-all-event', (eventData) => {
                    console.log('Received mute-all-event data:', eventData);
                    if (Array.isArray(eventData.data)) {
                        this.muteAllParticipants(eventData.data);
                    } else {
                        console.error('Data property is not an array:', eventData.data);
                    }
                });


        });

        // Event-Listener hinzufügen
        window.addEventListener('beforeunload', this.disconnectFromRoom);
    },
    beforeDestroy() {
        // Event-Listener entfernen, wenn die Komponente zerstört wird
        window.removeEventListener('beforeunload', this.disconnectFromRoom);
    }


};


</script>
