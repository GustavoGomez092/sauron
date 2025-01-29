import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import AdminApp from './AdminApp.vue'

if(document.querySelector('#struck')) createApp(App).mount('#struck')
if(document.querySelector('#struck-options')) createApp(AdminApp).mount('#struck-options')
