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

export default {
    data() {
        return {
            roomName: '',
            selectedRoom: '',
            accessToken: '',
            rooms: [],
            isConnected: false,
            username: `FakeUsername${Math.floor(Math.random() * 10000)}`,
            showMuteIcon: false,
            isAdmin: true,
            remoteParticipants: [],
        };
    },
    methods: {
        async createRoom() {
            try {
                const response = await axios.post('/api/room/create', { roomName: this.roomName });
                if (response.data.room) {
                    this.joinRoom();
                }
            } catch (error) {
                console.error('Error creating room:', error);
            }
        },
        attachTracks(participant) {
            participant.on('trackSubscribed', track => {
                document.getElementById('remote-videos').appendChild(track.attach());
            });
        },
        detachParticipantTracks(participant) {
            const participantContainer = document.getElementById(`participant-${participant.identity}`);
            if (participantContainer) {
                participantContainer.remove();
            }
        },
        attachParticipantTracks(participant) {
            const participantContainer = document.createElement('div');
            participantContainer.id = `participant-${participant.identity}`;
            participantContainer.classList.add('participant-container');

            const label = document.createElement('div');
            label.innerText = participant.identity;
            label.classList.add('participant-label');
            participantContainer.appendChild(label);

            participant.tracks.forEach(publication => {
                if (publication.isSubscribed) {
                    const track = publication.track;
                    const trackElement = track.attach();
                    trackElement.classList.add('participant');
                    participantContainer.appendChild(trackElement);
                }
            });

            participant.on('trackSubscribed', track => {
                const trackElement = track.attach();
                trackElement.classList.add('participant');
                participantContainer.appendChild(trackElement);
            });

            document.getElementById('remote-videos').appendChild(participantContainer);
        },
        async joinRoom() {
            try {
                const tokenResponse = await axios.post('/api/room/token', { roomName: this.roomName, identity: this.username });
                this.accessToken = tokenResponse.data.token;
                const room = await connect(this.accessToken, { name: this.roomName });
                currentRoom = room;
                this.isConnected = true;

                const localVideoContainer = document.getElementById('local-video');
                const localTracks = Array.from(room.localParticipant.tracks.values());
                localTracks.forEach(publication => {
                    if (publication.track && typeof publication.track.attach === 'function') {
                        localVideoContainer.appendChild(publication.track.attach());
                    }
                });

                room.on('participantConnected', participant => {
                    this.attachParticipantTracks(participant);
                    this.onParticipantConnected(participant);
                });

                room.on('participantDisconnected', participant => {
                    this.detachParticipantTracks(participant);
                    this.onParticipantDisconnected(participant);
                });

                room.participants.forEach(participant => {
                    this.attachParticipantTracks(participant);
                    this.onParticipantConnected(participant);
                });
            } catch (error) {
                console.error('Error joining room:', error);
            }
        },
        async fetchRooms() {
            try {
                const response = await axios.get('/api/rooms');
                this.rooms = response.data;
            } catch (error) {
                console.error('Error fetching rooms:', error);
            }
        },
        async submitForm() {
            if (this.selectedRoom) {
                this.roomName = this.selectedRoom;
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
                currentRoom.localParticipant.tracks.forEach(publication => {
                    publication.track.stop();
                    const attachedElements = publication.track.detach();
                    attachedElements.forEach(element => element.remove());
                });

                currentRoom.participants.forEach(participant => {
                    this.detachParticipantTracks(participant);
                });

                const localVideoContainer = document.getElementById('local-video');
                while (localVideoContainer.firstChild) {
                    localVideoContainer.firstChild.remove();
                }

                currentRoom.disconnect();
                currentRoom = null;
                this.isConnected = false;
            }
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
    },
    mounted() {
        this.fetchRooms();
        this.$nextTick(() => {
            new Masonry('#remote-videos', {
                itemSelector: '.participant-container',
                columnWidth: 200,
                gutter: 10
            });
        });

        window.addEventListener('beforeunload', this.disconnectFromRoom);
    },
    beforeDestroy() {
        window.removeEventListener('beforeunload', this.disconnectFromRoom);
    }
};


</script>
