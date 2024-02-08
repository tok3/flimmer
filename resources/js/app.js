// Importieren von Abhängigkeiten und Bibliotheken
import './bootstrap';
import { createApp } from 'vue';
import { Buffer } from 'buffer';
import process from 'process';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

/*window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true
});*/
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '116cebb4a453db4fb46d',
    cluster: 'eu',
    encrypted: true
});
// Globale Variablen und Polyfills
if (typeof global === 'undefined') {
    window.global = window;
}
window.Buffer = Buffer;
window.process = process;

// Erstellen einer neuen Vue-Instanz
const app = createApp({});

// Komponenten-Imports
import ExampleComponent from './components/ExampleComponent.vue';
//import VideoChat from './components/VideoChat.vue';
import AppVideoChat from './components/videochat/AppVideoChat.vue';
// Komponenten registrieren
app.component('example-component', ExampleComponent);
//app.component('video-chat', VideoChat);
//app.component('videochat/app-video-chat', VideoChat);
app.component('app-video-chat', AppVideoChat);
// Vue-App an ein HTML-Element anhängen
app.mount('#app');
