<script setup>
import HelloWorld from './components/HelloWorld.vue'
import LogTable from './components/LogTable.vue'
import {onBeforeMount, ref} from 'vue'
import axios from 'axios'

const data = ref()

const api = axios.create(
  {
    baseURL: `/wp-json/Sauron/v1/`,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      'X-WP-Nonce': window.wpApiSettings.nonce
    }
  }
)

const getUser = async() => {
  const response = await api.get('user')
  data.value = response.data
}

onBeforeMount(async () => {
  await getUser()
})

</script>

<template>
  <div v-if="data" class="tw-container tw-mx-auto tw-my-20">
    <LogTable />
  </div>
  
</template>

<style>
@tailwind base;
@tailwind components;
@tailwind utilities;


</style>
