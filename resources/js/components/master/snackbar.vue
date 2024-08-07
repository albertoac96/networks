<template>
    <div class="text-center">
      <v-btn
        dark
        color="red darken-2"
        @click="snackbar = true"
      >
        Open Snackbar
      </v-btn>
  
      <v-snackbar
        v-model="$store.state.snackbar.visible"
        multi-line
        centered
        shaped
      >
        {{ $store.state.snackbar.text }}
  
        <template v-slot:action="{ attrs }">
          <v-btn
            color="red"
            text
            v-bind="attrs"
            @click="$store.state.snackbar.visible = false"
          >
            Close
          </v-btn>
        </template>
      </v-snackbar>
    </div>
  </template>


  <script>
  export default {
    data: () => ({
      multiLine: true,
    }),
  }
</script>