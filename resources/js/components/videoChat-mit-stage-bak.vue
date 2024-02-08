<template>
    <div>
        <div>


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

                        <!--                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Raum erstellen</button>-->

                        <button type="submit"
                                class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24">
                                <path fill="currentColor"
                                      d="M480-480Zm320 320H600q0-20-1.5-40t-4.5-40h206v-480H160v46q-20-3-40-4.5T80-680v-40q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160Zm-720 0v-120q50 0 85 35t35 85H80Zm200 0q0-83-58.5-141.5T80-360v-80q117 0 198.5 81.5T360-160h-80Zm160 0q0-75-28.5-140.5t-77-114q-48.5-48.5-114-77T80-520v-80q91 0 171 34.5T391-471q60 60 94.5 140T520-160h-80Z"/>
                            </svg>
                            <span class="sr-only">Raum bereten cast starten</span>
                        </button>

                        <!--                    <button v-else @click="disconnectFromRoom" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Disconnect</button>-->


                    </form>

                    <div class="remote-participants-list">
                        <ul>
                            <li v-for="participant in remoteParticipants" :key="participant.sid" :id="`plist-${participant.sid}`">
                                Name: {{ participant.identity }},

                                <span v-if="participant.isMuted" v-html="getIconWithColor(baseMutedIcon,'#ff0000')" @click="toggleParticipantMute(participant.sid)"></span>

                                <span v-if="participant.isHandRaised" v-html="getIconWithColor(icoHandRaised,'#000000')"></span>
                                <!-- Weitere Informationen oder Icons -->
                                <span @click="toggleStaging(participant)" v-html="getIconWithColor(icoStagedBase,'#ff0000')"></span>

                                <button @click="toggleStaging(participant.sid)">{{ participant.isStaged ? 'Von Bühne nehmen' : 'Auf Bühne stellen' }}</button>
                            </li>

                        </ul>
                    </div>

                    <button v-if="isConnected" @click="disconnectFromRoom"
                            class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24">
                            <path fill="currentColor"
                                  d="m336-280 144-144 144 144 56-56-144-144 144-144-56-56-144 144-144-144-56 56 144 144-144 144 56 56ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm0-560v560-560Z"/>
                        </svg>
                        <span class="sr-only">Disconnect</span>
                    </button>


                    <!--                    <button v-if="isConnected && isAdmin" @click="muteAll" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Alle stummschalten</button>-->

                    <button v-if="isConnected && isAdmin" @click="muteAll" type="button"
                            class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">

                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24">
                            <path fill="currentColor"
                                  d="m710-362-58-58q14-23 21-48t7-52h80q0 44-13 83.5T710-362ZM480-594Zm112 112-72-72v-206q0-17-11.5-28.5T480-800q-17 0-28.5 11.5T440-760v126l-80-80v-46q0-50 35-85t85-35q50 0 85 35t35 85v240q0 11-2.5 20t-5.5 18ZM440-120v-123q-104-14-172-93t-68-184h80q0 83 57.5 141.5T480-320q34 0 64.5-10.5T600-360l57 57q-29 23-63.5 39T520-243v123h-80Zm352 64L56-792l56-56 736 736-56 56Z"/>
                        </svg>
                        Mute all Participants
                        <span class="sr-only">Mute All</span>
                    </button>


                </div>

            </div>


            <!-- Hier können Sie ein Formular zum Erstellen eines Raums und zum Anzeigen des Videos hinzufügen -->
            <div class="grid grid-cols-4">
                <div>
                    <div id="local-video" class="video-container">

                        <!-- Lokales Video hier -->
                    </div>


                </div>
                <!-- ... -->
                <div v-if="isConnected">

                    <button @click="toggleMute" type="button"
                            class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">

                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24">
                            <path fill="currentColor"
                                  d="M480-400q-50 0-85-35t-35-85v-240q0-50 35-85t85-35q50 0 85 35t35 85v240q0 50-35 85t-85 35Zm0-240Zm-40 520v-123q-104-14-172-93t-68-184h80q0 83 58.5 141.5T480-320q83 0 141.5-58.5T680-520h80q0 105-68 184t-172 93v123h-80Zm40-360q17 0 28.5-11.5T520-520v-240q0-17-11.5-28.5T480-800q-17 0-28.5 11.5T440-760v240q0 17 11.5 28.5T480-480Z"/>
                        </svg>
                        Mikrofon Ein/Aus
                        <span class="sr-only">Mute</span>
                    </button>


                    <button @click="raiseHand" type="button"
                            class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24">
                            <path fill="currentColor"
                                  d="M280-480v-360q0-17 11.5-28.5T320-880q17 0 28.5 11.5T360-840v360h-80Zm160 0v-400q0-17 11.5-28.5T480-920q17 0 28.5 11.5T520-880v400h-80Zm160 163v-483q0-17 11.5-28.5T640-840q17 0 28.5 11.5T680-800v436l-80 47ZM280-120h267q8 0 15-3.5t13-8.5l182-182-287 167-130-173H200v120q0 33 23.5 56.5T280-120Zm0 80q-66 0-113-47t-47-113v-200h260l110 147 190-111 90-52q21-12 44.5-10.5T858-411l63 47L632-75q-17 17-39 26t-46 9H280Zm-80-360h-80v-360q0-17 11.5-28.5T160-800q17 0 28.5 11.5T200-760v360Zm0-80h400-400Zm80 360h-80 375-295Z"/>
                        </svg>
                        <span class="sr-only">Icon description</span>
                    </button>

                </div>
            </div>

            <!--
                        <div id="remote-videos" class="video-container">
                            &lt;!&ndash; Entfernte Videos hier &ndash;&gt;
                        </div>
            -->
            <div class="video-area">
                <div id="stage-area" class="stage-container" style="border:1px dashed green !important;">
                    <div v-for="participant in remoteParticipants"
                         v-if="participant && participant.isStaged"
                         :key="participant.sid"
                         class="stage-participant-container">
                        <!-- Hier wird das Video oder der Track des gestagten Teilnehmers angehängt -->
                    </div>
                </div>


                <div id="remote-videos" class="video-container" style="border:1px dashed red !important;">
                    <div v-for="participant in remoteParticipants"
                         v-if="participant && !participant.isStaged"
                         :key="participant.sid"
                         class="participant-container">
                        <!-- Hier wird das Video oder der Track des Teilnehmers angehängt -->
                    </div>
                </div>

            </div>

            <!--            &lt;!&ndash; Publikumsbereich &ndash;&gt;
                        <div  v-if="isConnected" id="remote-videos" class="video-container">
                            <div v-for="participant in remoteParticipants" v-if="!participant.isStaged" class="participant-container">
                                &lt;!&ndash; Audience Participant Video &ndash;&gt;
                            </div>
                        </div>-->

        </div>

    </div>
    <svg width="24px" height="24px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
        <path fill="#ffffff"
              d="m412.16 592.128-45.44 45.44A191.232 191.232 0 0 1 320 512V256a192 192 0 1 1 384 0v44.352l-64 64V256a128 128 0 1 0-256 0v256c0 30.336 10.56 58.24 28.16 80.128zm51.968 38.592A128 128 0 0 0 640 512v-57.152l64-64V512a192 192 0 0 1-287.68 166.528l47.808-47.808zM314.88 779.968l46.144-46.08A222.976 222.976 0 0 0 480 768h64a224 224 0 0 0 224-224v-32a32 32 0 1 1 64 0v32a288 288 0 0 1-288 288v64h64a32 32 0 1 1 0 64H416a32 32 0 1 1 0-64h64v-64c-61.44 0-118.4-19.2-165.12-52.032zM266.752 737.6A286.976 286.976 0 0 1 192 544v-32a32 32 0 0 1 64 0v32c0 56.832 21.184 108.8 56.064 148.288L266.752 737.6z"/>
        <path fill="#ffffff" d="M150.72 859.072a32 32 0 0 1-45.44-45.056l704-708.544a32 32 0 0 1 45.44 45.056l-704 708.544z"/>
    </svg>


</template>


<style>
.video-area {
    display: flex;
    flex-direction: column;
}
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
    width: 100px; /* Oder die gewünschte Größe */
    height: 75px; /* Oder die gewünschte Größe */
    object-fit: cover;
    margin: 10px;
}

/* Stile für den Container der normalen Teilnehmer */
.participant-container {
    width: 200px;
    height: 150px;
    position: relative; /* Wichtig für die korrekte Positionierung der Labels */
}


.participant-container video, .stage-participant-container video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.stage-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start; /* Damit Videos nicht gestreckt werden */
    gap: 10px;
    margin-bottom: 20px; /* Abstand zwischen der Bühne und den remote-videos */
}



/*
.stage-participant-container video {
     width: 100%;
     height: 100%;
     object-fit: cover;
 }
*/

/* Stile für das Label mit dem Benutzernamen */
.participant-label {
    display: flex;
    align-items: center;
    position: absolute;
    bottom: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    padding: 5px 10px;
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

.local-raised-icon {
    /* Ihr Stil für das lokale Mute-Icon */
    position: absolute;
    top: -5px;
    right: -5px;
    width: 24px;
    height: 24px;
    z-index: 2;
}


.hand-raise-icon {
    /* Ihr Stil für das lokale Mute-Icon */
    position: absolute;
    top: 5px;
    right: 5px;
    width: 24px;
    height: 24px;
    z-index: 2;
}

.mute-icon {
    cursor: pointer;
    /* Weitere Stile nach Bedarf */
}

.remote-participants-list li {
    display: flex;
    align-items: center;
    white-space: nowrap;
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
import Twilio from 'twilio-video';

let currentRoom = null;
let isHandRaised = false;
let roomSid = false;


let baseMutedIcon = `<svg width="24px" height="24px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><path fill="%FILL_COLOR%" d="m412.16 592.128-45.44 45.44A191.232 191.232 0 0 1 320 512V256a192 192 0 1 1 384 0v44.352l-64 64V256a128 128 0 1 0-256 0v256c0 30.336 10.56 58.24 28.16 80.128zm51.968 38.592A128 128 0 0 0 640 512v-57.152l64-64V512a192 192 0 0 1-287.68 166.528l47.808-47.808zM314.88 779.968l46.144-46.08A222.976 222.976 0 0 0 480 768h64a224 224 0 0 0 224-224v-32a32 32 0 1 1 64 0v32a288 288 0 0 1-288 288v64h64a32 32 0 1 1 0 64H416a32 32 0 1 1 0-64h64v-64c-61.44 0-118.4-19.2-165.12-52.032zM266.752 737.6A286.976 286.976 0 0 1 192 544v-32a32 32 0 0 1 64 0v32c0 56.832 21.184 108.8 56.064 148.288L266.752 737.6z"/><path fill="%FILL_COLOR%" d="M150.72 859.072a32 32 0 0 1-45.44-45.056l704-708.544a32 32 0 0 1 45.44 45.056l-704 708.544z"/></svg>`;


let icoHandRaised = '<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path fill="%FILL_COLOR%" d="M280-480v-360q0-17 11.5-28.5T320-880q17 0 28.5 11.5T360-840v360h-80Zm160 0v-400q0-17 11.5-28.5T480-920q17 0 28.5 11.5T520-880v400h-80Zm160 163v-483q0-17 11.5-28.5T640-840q17 0 28.5 11.5T680-800v436l-80 47ZM280-120h267q8 0 15-3.5t13-8.5l182-182-287 167-130-173H200v120q0 33 23.5 56.5T280-120Zm0 80q-66 0-113-47t-47-113v-200h260l110 147 190-111 90-52q21-12 44.5-10.5T858-411l63 47L632-75q-17 17-39 26t-46 9H280Zm-80-360h-80v-360q0-17 11.5-28.5T160-800q17 0 28.5 11.5T200-760v360Zm0-80h400-400Zm80 360h-80 375-295Z"/></svg>';


let icoStagedBase = '\n' +
    '<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path fill="%FILL_COLOR%" d="M500-780q0 33-23.5 56.5T420-700q-13 0-24-3.5T374-715q-24 8-38.5 29T321-640h519l-40 280H604v-80h127q5-30 8.5-60t8.5-60H212q5 30 8.5 60t8.5 60h127v80H160l-40-280h120q0-49 27-89t73-59q3-31 26-51.5t54-20.5q33 0 56.5 23.5T500-780ZM391-200h178l23-240H368l23 240Zm-71 80-30-312q-4-35 20-61.5t59-26.5h222q35 0 59 26.5t20 61.5l-30 312H320Z"/></svg>';
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
            isStaged: false,
            isAdmin: true,
            remoteParticipants: [],
            icoHandRaised: icoHandRaised,
            isHandRaised: isHandRaised,
            baseMutedIcon:baseMutedIcon,
            icoStagedBase:icoStagedBase,

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

                const muteIcon = this.createMuteIcon(participantSid);

                label.appendChild(muteIcon);

                // icon in teilnehmerliste aktualisieren
                this.updateRemoteParticipantMuteStatus(participantSid, true);

            } else {
                console.warn(`Teilnehmer-Container für ${participantSid} nicht gefunden.`);
            }


        },

        createMuteIcon(participantSid) {
            const muteIcon = document.createElement('span');
            muteIcon.innerHTML = this.getIconWithColor(baseMutedIcon,'#ffffff'); // icon an video container hinter name
            muteIcon.classList.add('mute-icon');
            muteIcon.addEventListener('click', () => this.toggleParticipantMute(participantSid));
            return muteIcon;
        },
        removeMuteIcon(participantSid) {
            const participantContainer = document.getElementById(`participant-${participantSid}`);
            if (participantContainer) {
                const muteIcon = participantContainer.querySelector('.mute-icon');
                if (muteIcon) {
                    muteIcon.remove();


                    // icon in teilnehmerliste aktualisieren
                    this.updateRemoteParticipantMuteStatus(participantSid, false);
                }
            } else {
                console.warn(`Teilnehmer-Container für ${participantSid} nicht gefunden.`);
            }
        },
        getIconWithColor(baseIcon, color) {
            return baseIcon
                .replace(/%FILL_COLOR%/g, color);
        },
        // Hilfsfunktion zum Anhängen von Tracks und Hinzufügen des Labels
        attachParticipantTracks(participant) {
            // Prüfen, ob der Teilnehmer 'isStaged' ist
            const participantData = this.remoteParticipants.find(p => p.sid === participant.sid);
            const isParticipantStaged = participantData ? participantData.isStaged : false;


            // Wenn der Teilnehmer 'isStaged' ist, fahren Sie nicht fort, den Container zu erstellen
            if (isParticipantStaged) {
                return;
            }

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
            this.manageParticipantMuteIcon(participant, participantContainer);

            // Teilnehmer-Tracks verwalten
            this.manageParticipantTracks(participant, participantContainer);

            // Fügen Sie den Container zum DOM hinzu
            document.getElementById('remote-videos').appendChild(participantContainer);
        },

        manageParticipantMuteIcon(participant, container) {
            const audioTrackPublication = participant.audioTracks.values().next().value;
            if (audioTrackPublication && audioTrackPublication.track) {
                if (!audioTrackPublication.track.isEnabled) {
                    this.addMuteIcon(participant.sid);
                }
            } else {
                console.warn("AudioTrackPublication oder AudioTrack fehlt für den Teilnehmer:", participant.identity);
            }
        },

        manageParticipantTracks(participant, container) {
            participant.tracks.forEach(publication => {
                if (publication.isSubscribed) {
                    this.addTrackToContainer(publication.track, container);
                }
            });

            // Event-Handler für das Abonnieren von Tracks
            participant.on('trackSubscribed', track => {
                this.addTrackToContainer(track, container);
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
        },

        addTrackToContainer(track, container) {
            if (track.kind === 'video' || track.kind === 'audio') {
                const trackElement = track.attach();
                trackElement.classList.add('participant-track');
                container.appendChild(trackElement);

                // Überprüfen Sie den Mute-Status und zeigen Sie das Mute-Icon an, wenn der Audio-Track deaktiviert ist
                if (track.kind === 'audio' && !track.isEnabled) {
                    this.addMuteIcon(track.participant.sid);
                }
            }
        },

        // Funktion zum Hinzufügen des Mute-Icons zum lokalen Video
        addLocalMuteIcon() {
            const localVideoContainer = document.getElementById('local-video');
            const muteIcon = document.createElement('span');
            muteIcon.innerHTML = this.getIconWithColor(baseMutedIcon,'#ff0000'); // Ihr SVG-Icon hier
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

        remoteHandUp(participantSid) {


            if (this.localParticipantSid == participantSid) {
                this.localHandUp(participantSid)
            }

            const participantContainer = document.getElementById(`participant-${participantSid}`);

            if (!participantContainer) {
                console.warn(`Teilnehmer-Container für ${participantSid} nicht gefunden.`);
                return;
            }

            let handraiseIcon = participantContainer.querySelector('.hand-raise-icon');
            if (!handraiseIcon) {
                handraiseIcon = document.createElement('span');
                handraiseIcon.innerHTML = this.getIconWithColor(icoHandRaised,'#000000'); // Ihr SVG-Icon hier
                handraiseIcon.classList.add('hand-raise-icon');
                participantContainer.appendChild(handraiseIcon);
            }


        },

        remoteHandDown(participantSid) {

            if (this.localParticipantSid == participantSid) {
                this.localHandDown(participantSid);
            }

            const participantContainer = document.getElementById(`participant-${participantSid}`);

            if (!participantContainer) {
                console.warn(`Teilnehmer-Container für ${participantSid} nicht gefunden.`);
                return;
            }

            let handraiseIcon = participantContainer.querySelector('.hand-raise-icon');
            // Wenn die Hand nicht gehoben ist, entfernen Sie das Icon, falls es existiert
            if (handraiseIcon) {
                participantContainer.removeChild(handraiseIcon);

            }

            // Aktualisieren des Handstatus in der Liste
            this.updateRemoteParticipantHandStatus(participantSid, false);
        },
        localHandUp(participantSid) {

            const localVideoContainer = document.getElementById('local-video');

            // Das Icon ist nicht vorhanden, fügen Sie es hinzu
            const newRaisedIcon = document.createElement('span');
            newRaisedIcon.innerHTML = this.getIconWithColor(icoHandRaised,'#ff0000'); // Ihr SVG-Icon hier
            newRaisedIcon.classList.add('local-raised-icon');
            localVideoContainer.appendChild(newRaisedIcon);

            this.isHandRaised = true;
        },
        localHandDown(participantSid) {

            console.error('local down');
            const localVideoContainer = document.getElementById('local-video');
            const raisedIcons = localVideoContainer.querySelectorAll('.local-raised-icon');

            raisedIcons.forEach(raisedIcon => {
                raisedIcon.remove();
            });

            this.isHandRaised = false;

        },
        async joinRoom() {
            try {
                // Token von Ihrem Server abrufen
                const tokenResponse = await axios.post('/api/room/token', {
                    roomName: this.roomName,
                    identity: this.username
                });
                this.accessToken = tokenResponse.data.token;

                const localDataTrack = new Twilio.LocalDataTrack();
                const localTracks = await Twilio.createLocalTracks({
                    audio: true,
                    video: {width: 640},
                    data: true
                });

                localTracks.push(localDataTrack);

                const room = await Twilio.connect(this.accessToken, {
                    name: this.roomName,
                    tracks: localTracks
                });

                this.roomSid = room.sid;
                console.error('room sid: ' + room.sid);
                currentRoom = room;

                // Speichern Sie die SID des lokalen Teilnehmers und setzen Sie den Verbindungsstatus
                this.localParticipantSid = room.localParticipant.sid;
                this.isConnected = true;

                // Lokales Video anhängen
                const localVideoContainer = document.getElementById('local-video');
                localTracks.forEach(track => {
                    if (track.kind === 'video') {
                        localVideoContainer.appendChild(track.attach());
                    }
                });


                room.on('participantConnected', participant => {
                    participant.on('trackSubscribed', track => {
                        if (track.kind === 'data') {
                            track.on('message', message => {
                                this.handleDataMessage(message, participant);
                            });
                        }

                    });

                    console.log(`Teilnehmer "${participant.identity}" verbunden`);
                    this.attachParticipantTracks(participant);
                    this.onParticipantConnected(participant);


                });

                room.on('participantDisconnected', participant => {
                    console.log(`Teilnehmer "${participant.identity}" getrennt`);
                    this.detachParticipantTracks(participant);
                    this.onParticipantDisconnected(participant);

                    this.removeRemoteParticipant(participant);
                });

                // Bereits vorhandene Teilnehmer behandeln und anzeigen
                room.participants.forEach(participant => {
                    console.log(`Bereits im Raum: "${participant.identity}"`);
                    this.attachParticipantTracks(participant);
                    this.onParticipantConnected(participant);

                    // Abonnieren Sie DataTracks von bereits vorhandenen Teilnehmern
                    participant.tracks.forEach(publication => {
                        if (publication.kind === 'data' && publication.isSubscribed) {
                            const dataTrack = publication.track;
                            dataTrack.on('message', message => {
                                this.handleDataMessage(message, participant);
                            });
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
                // Stoppen und Entfernen aller lokalen Tracks
                currentRoom.localParticipant.tracks.forEach(publication => {
                    if (publication.track && typeof publication.track.stop === 'function') {
                        publication.track.stop();
                        const attachedElements = publication.track.detach();
                        attachedElements.forEach(element => element.remove());
                    }
                });

                // Entfernen aller entfernten Teilnehmer-Tracks
                currentRoom.participants.forEach(participant => {
                    this.detachParticipantTracks(participant);
                });

                // Entfernen des Mute-Icons vom lokalen Video
                const localVideoContainer = document.getElementById('local-video');
                const muteIcon = localVideoContainer.querySelector('.local-mute-icon');
                if (muteIcon) muteIcon.remove();

                // Entfernen aller Kinder von local-video
                while (localVideoContainer.firstChild) {
                    localVideoContainer.firstChild.remove();
                }

                // Trennen der Verbindung zum Raum und Zurücksetzen der currentRoom-Variable
                currentRoom.disconnect();
                currentRoom = null;

                // Zurücksetzen des isConnected-Status
                this.isConnected = false;

                // Leeren der Liste der entfernten Teilnehmer
                this.remoteParticipants = [];
            }
        },
        removeRemoteParticipant(participant) {
            this.remoteParticipants = this.remoteParticipants.filter(p => p.sid !== participant.sid);
        },
        muteParticipant(participantSid) {

            const localAudioTrack = Array.from(currentRoom.localParticipant.audioTracks.values())[0].track;
            if ((participantSid === this.localParticipantSid) && localAudioTrack.isEnabled) {
                localAudioTrack.disable();
                this.addLocalMuteIcon();

            }

        }, muteToggleParticipant(participantSid) {

            // funktion wird derzeit nur zum unmuten benutzt
            const localAudioTrack = Array.from(currentRoom.localParticipant.audioTracks.values())[0].track;
            if ((participantSid === this.localParticipantSid) && (localAudioTrack.isEnabled == false)) {

                localAudioTrack.enable();
                this.removeLocalMuteIcon();

            }

        },
        muteAllParticipants(participants) {
            console.log('participants: ' + participants)

            participants.forEach(participant => {

                this.updateRemoteParticipantMuteStatus(participant.sid, true);
                this.muteParticipant(participant.sid);

            });
        },
        onParticipantConnected(participant) {
            console.log('onParticipantConnected');
            this.remoteParticipants.push({
                identity: participant.identity,
                sid: participant.sid,
                isMuted: false, // Standardmäßig nicht stummgeschaltet
                isHandRaised: false, // Standardmäßig Hand nicht gehoben
                isStaged: false, // Standardmäßig Hand nicht gehoben
            });

            this.fetchHandStatuses(this.roomSid);
        },
        onParticipantDisconnected(participant) {
            const index = this.remoteParticipants.indexOf(participant);
            if (index !== -1) {
                this.remoteParticipants.splice(index, 1);
            }
        },
        async muteAll() {
            try {
                console.error("remote participants" + this.remoteParticipants);
                const response = await axios.post('/trigger-mute-all', {
                    participants: this.remoteParticipants
                });

                console.log('MuteAll Event ausgelöst:', response.data.message);
            } catch (error) {
                console.error('Fehler beim Auslösen des MuteAll-Events:', error);
            }
        },
        async toggleParticipantMute(participantSid) {
            try {
                const response = await axios.post('/trigger-toggle-mute', {
                    participants: participantSid
                });

                console.log('toggle mute Event ausgelöst:', participantSid);
            } catch (error) {
                console.error('Fehler beim Auslösen des MuteAll-Events:', error);
            }
        },
        async raiseHand() {
            try {
                if (this.isHandRaised == false) {
                    console.info('EVENT /trigger-hand-raise, isHandRaised:' + this.isHandRaised);
                    const response = await axios.post('/trigger-hand-raise', {
                        participant: this.localParticipantSid,
                        roomId: this.roomSid,
                    });


                    this.isHandRaised = true;
                } else {
                    console.info('EVENT /cleanup-hand-raise isHandRaised:' + this.isHandRaised);

                    const response = await axios.post('/cleanup-hand-raise', {
                        participant: this.localParticipantSid,
                        roomId: this.roomSid,
                    });

                    this.isHandRaised = false;

                }
                console.log('Hand Raise Event ausgelöst:', this.roomSid + '<-->' + this.localParticipantSid);
            } catch (error) {
                console.error('Fehler beim Hand Bewegen', error);
            }
        },
        async fetchHandStatuses(roomId) {
            try {
                const response = await axios.get(`/room/${roomId}/hand-statuses`);
                this.setHandStatuses(response.data);
            } catch (error) {
                console.error('Error fetching hand statuses:', error);
            }
        },

        setHandStatuses(statuses) {
            // Iterieren über die Schlüssel im statuses-Objekt
            for (const key in statuses) {
                if (statuses.hasOwnProperty(key)) {
                    // Den Schlüssel an die remoteHandUp-Funktion übergeben
                    this.remoteHandUp(key);
                }
            }
        },
        updateRemoteParticipantHandStatus(participantSid, isHandRaised) {
            const participantIndex = this.remoteParticipants.findIndex(p => p.sid === participantSid);
            if (participantIndex !== -1) {
                // Direkte Zuweisung der neuen Werte
                this.remoteParticipants[participantIndex].isHandRaised = isHandRaised;
            }
        },

        updateRemoteParticipantMuteStatus(participantSid, isMuted) {
            const participantIndex = this.remoteParticipants.findIndex(p => p.sid === participantSid);
            if (participantIndex !== -1) {
                this.remoteParticipants[participantIndex].isMuted = isMuted;
            }
        },
        toggleStaging(participantSid) {
            const participantIndex = this.remoteParticipants.findIndex(p => p.sid === participantSid);
            if (participantIndex !== -1) {
                this.remoteParticipants[participantIndex].isStaged = !this.remoteParticipants[participantIndex].isStaged;
                this.moveParticipantVideo(participantSid, this.remoteParticipants[participantIndex].isStaged);
            }
        },

        moveParticipantVideo(participantSid, isStaged) {
            const targetContainerId = isStaged ? 'stage-area' : 'remote-videos';
            const participantElement = document.getElementById(`participant-${participantSid}`);

            if (participantElement) {
                const targetContainer = document.getElementById(targetContainerId);
                targetContainer.appendChild(participantElement);
            }
        },

        rearrangeVideos() {
            /*  new Masonry('#remote-videos', {
                  itemSelector: '.participant-container',
                  columnWidth: 200,
                  gutter: 10
              });

              new Masonry('#stage-area', {
                  itemSelector: '.stage-participant-container',
                  columnWidth: 200,
                  gutter: 10
              });*/
        },

    },

    mounted() {
        this.fetchRooms();
        this.$nextTick(() => {
            /*  new Masonry('#remote-videos', {
                  itemSelector: '.participant-container',
                  columnWidth: 200,
                  gutter: 10
              });*/


            window.Echo.channel('mute-all-channel')
                .listen('.mute-all-event', (eventData) => {
                    console.log('Received mute-all-event data:', eventData);
                    if (Array.isArray(eventData.data)) {
                        this.muteAllParticipants(eventData.data);
                    } else {
                        console.error('Data property is not an array:', eventData.data);
                    }
                });

            window.Echo.channel('toggle-mute-channel')
                .listen('.toggle-mute-event', (eventData) => {
                    console.log('Received toggle-mute-channel:', eventData.sid);
                    this.muteToggleParticipant(eventData.sid);

                });

            window.Echo.channel('hand-raise-channel')
                .listen('.hand-raise-event', (eventData) => {

                    /*console.error('Received hand-raise-channel:', eventData);
                    this.toggleHandRaise(eventData.sid);*/

                    // Iteriere über die Schlüssel des "sid"-Objekts in eventData
                    for (const participantSid in eventData.sid) {
                        if (eventData.sid.hasOwnProperty(participantSid)) {
                            const value = eventData.sid[participantSid];

                            console.log('hand ' + value + ': ' + participantSid);

                            this.updateRemoteParticipantHandStatus(participantSid, value);
                            if (value === true) {
                                this.remoteHandUp(participantSid);
                            } else {
                                this.remoteHandDown(participantSid);
                            }
                        }
                    }

                });


        });

        // Event-Listener hinzufügen
        window.addEventListener('beforeunload', this.disconnectFromRoom);


    },
    beforeDestroy() {
        // Event-Listener entfernen, wenn die Komponente zerstört wird
        window.removeEventListener('beforeunload', this.disconnectFromRoom);
    },


};

</script>
