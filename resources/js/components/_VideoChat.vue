<template>
    <div class="p-5">
        <h1 class="text-2xl mb-4">Laravel Video Chat</h1>
        <div class="grid grid-flow-row grid-cols-3 grid-rows-3 gap-4 bg-black/]">
            <div id="my-video-chat-window"></div>
        </div>
    </div>


    <form @submit.prevent="createRoom">
        <input v-model="roomName" placeholder="Raumname eingeben" />
        <button type="submit">Raum erstellen</button>
    </form>

</template>

<script>
import axios from 'axios';

export default {
    name: 'video-chat',
    data() {
        return {
            accessToken: '',
            roomName: '',
        };
    },
    methods: {
        async getAccessToken() {
            try {
                const response = await axios.get('/api/access_token');
                this.accessToken = response.data;
                console.log(this.accessToken);
            } catch (error) {
                console.error(error);
            }
        },
        async connectToRoom() {
            try {
                const room = await connect(this.accessToken, { name: 'cool room' });

                console.log(`Successfully joined a Room: ${room}`);

                const videoChatWindow = document.getElementById('video-chat-window');
                const track = await createLocalVideoTrack();
                videoChatWindow.appendChild(track.attach());

                room.on('participantConnected', participant => {


                    console.log(`Participant "${participant.identity}" connected`);

                    participant.tracks.forEach(publication => {
                        if (publication.isSubscribed) {
                            const track = publication.track;
                            videoChatWindow.appendChild(track.attach());
                        }
                    });

                    participant.on('trackSubscribed', track => {
                        videoChatWindow.appendChild(track.attach());
                    });

                });
            } catch (error) {
                console.error(`Unable to connect to Room: ${error.message}`);
            }
        },
        async createRoom() {
            try {
                const response = await axios.post('/room/create', {
                    roomName: this.roomName
                });

                if (response.data.success) {
                    console.log('Raum erfolgreich erstellt');
                    console.log(this.getRooms());
                } else {
                    console.error('Fehler beim Erstellen des Raums');
                }
            } catch (error) {
                console.error('Es gab einen Fehler bei der Anfrage:', error);
            }
        },
        async getRooms() {
            try {
                const response = await axios.get('/rooms/get_rooms');
                const rooms = response.data.rooms;
                console.log('Verfügbare Räume:', rooms);
            } catch (error) {
                console.error('Fehler beim Abrufen der Räume:', error);
            }
        }
    },
    async mounted() {
        console.log('Video chat room loading...');
        await this.getAccessToken();
    }
};

</script>
