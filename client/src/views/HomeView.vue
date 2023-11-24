<script setup>
if(window.isSecureContext) {
    console.log('Using secure context.');
  }
</script>

<template>
  <div class="home">
    Upload a file!

    <h1>Development Todo</h1>

    <ul style="width: 250px; margin: 0 auto; text-align: left">
      <li><s>Generate / Load a keypair</s></li>
      <li><s>Test signing arbitrary data</s></li>
      <li>Sign a challenge from the API</li>
      <li>Actually upload a file!!!</li>
      <li>Implement user registration</li>
    </ul>

    <hr style="margin: 1em 0" />

    <div>
      <button @click="generateKeyPair">Generate Key Pair</button>
      <button @click="fetchChallenge">Fetch API Challenge</button>
      <button @click="signChallenge">Sign Challenge</button>
    </div>

    <div>
      <textarea rows="5" cols="60">{{ pubKey }}</textarea>
    </div>

    <div>
      <textarea rows="5" cols="60">{{ challenge }}</textarea>
    </div>

    <div>
      <textarea rows="5" cols="60">{{ signedChallenge }}</textarea>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';

const keyPair = ref({});
const pubKey = ref('');
const challenge = ref('');
const signedChallenge = ref('');

function ab2str(buf) {
  return String.fromCharCode.apply(null, new Uint8Array(buf));
}

export default {
  name: 'Home',
  components: {
  },

  methods: {
    async generateKeyPair() {
      const generatedKey = await crypto.subtle.generateKey(
        {
          name: "RSA-PSS",
          modulusLength: 4096,
          publicExponent: new Uint8Array([1, 0, 1]),
          hash: "SHA-256",
        },

        true,

        ["sign", "verify"] // Permissions needed by this key
      );

      keyPair.value = generatedKey;
      this.getPubkey();
    },

    async getPubkey() {
      const exported = await window.crypto.subtle.exportKey(
       "spki",
       keyPair.value.publicKey
     );

      const exportedAsString = ab2str(exported);
      const exportedAsBase64 = window.btoa(exportedAsString);

      pubKey.value = `-----BEGIN PUBLIC KEY-----\n${exportedAsBase64}\n-----END PUBLIC KEY-----`;
    },

    async fetchChallenge() {
      const options = {
        method: "POST",
        headers: {
          "Accept": "application/json",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({pubkey: pubKey.value}),
      };

      const response = await fetch("http://upload.local/api/v1/challenge", options);
      challenge.value = await response.json();
    },

    async signChallenge() {
      let encoder = new TextEncoder();
      let encodedMessage = encoder.encode(challenge.value);

      let signature = await window.crypto.subtle.sign(
        {
          name: "RSA-PSS",
          saltLength: 32,
        },
        keyPair.value.privateKey,
        encodedMessage
      );

      let buffer = new Uint8Array(signature, 0, 512);

      const signatureAsString = ab2str(buffer);
      const signatureAsBase64 = window.btoa(signatureAsString);

      signedChallenge.value = signatureAsBase64;
    }
  },
}
</script>
