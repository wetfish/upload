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
      <li><s>Sign a challenge from the API</s></li>
      <li><s>Actually upload a file!!!</s></li>
      <li><s>Debug max file upload size</s></li>
      <li><s>Test multiple file uploads</s></li>
      <li>Display all uploaded files on home page</li>
      <li>Implement user registration</li>
    </ul>

    <hr style="margin: 1em 0" />

    <button @click="generateKeyPair">Generate Key Pair</button>

    <div>
      <textarea rows="5" cols="60">{{ privkey }}</textarea>
    </div>

    <div>
      <textarea rows="5" cols="60">{{ pubkey }}</textarea>
    </div>

    <button @click="fetchChallenge">Fetch API Challenge</button>

    <div>
      <textarea rows="5" cols="60">{{ challenge }}</textarea>
    </div>

    <button @click="signChallenge">Sign Challenge</button>

    <div>
      <textarea rows="5" cols="60">{{ signedChallenge }}</textarea>
    </div>

    <div>
      <input type="file" multiple ref="file">
      <button @click="upload">Upload</button>
    </div>

    <div v-if="uploadedFiles">
      <p>
        File(s) uploaded!!
      </p>

      <div v-for="(uploadedFile, index) in uploadedFiles">
        <img :src="uploadedFile" />
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';

const keyPair = ref({});
const privkey = ref('');
const pubkey = ref('');
const challenge = ref('');
const signedChallenge = ref('');
const file = ref();
const uploadedFiles = ref([]);

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
      this.getPrivkey();
      this.getPubkey();
    },

    async getPrivkey() {
      const exported = await window.crypto.subtle.exportKey(
       "pkcs8",
       keyPair.value.privateKey
     );

      const exportedAsString = ab2str(exported);
      const exportedAsBase64 = window.btoa(exportedAsString);

      privkey.value = `-----BEGIN PRIVATE KEY-----\n${exportedAsBase64}\n-----END PRIVATE KEY-----`;
    },

    async getPubkey() {
      const exported = await window.crypto.subtle.exportKey(
       "spki",
       keyPair.value.publicKey
     );

      const exportedAsString = ab2str(exported);
      const exportedAsBase64 = window.btoa(exportedAsString);

      pubkey.value = `-----BEGIN PUBLIC KEY-----\n${exportedAsBase64}\n-----END PUBLIC KEY-----`;
    },

    async fetchChallenge() {
      const options = {
        method: "POST",
        headers: {
          "Accept": "application/json",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({pubkey: pubkey.value}),
      };

      const response = await fetch("http://upload.local/api/v1/challenge", options);
      const responseJson = await response.json();
      challenge.value = responseJson.challenge;
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
    },

    async upload() {
      const data = new FormData();
      data.append('challenge', challenge.value);
      data.append('signature', signedChallenge.value);

      for (const selectedFile of file.value.files) {
        data.append('file[]', selectedFile);
      }

      const options = {
        method: "POST",
        headers: {
          "Accept": "application/json",
        },
        body: data,
      };

      const response = await fetch('http://upload.local/api/v1/file', options);
      const responseJson = await response.json();

      uploadedFiles.value.push(...responseJson.uploadedFiles);
    }
  },
}
</script>
