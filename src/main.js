import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import AdminApp from './AdminApp.vue'

if(document.querySelector('#Sauron')) createApp(App).mount('#Sauron')
if(document.querySelector('#Sauron-options')) createApp(AdminApp).mount('#Sauron-options')
