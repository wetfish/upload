<template>
  <div class="home">
    Upload a file!

    <h1>Development Todo</h1>

    <ul style="width: 250px; margin: 0 auto; text-align: left">
      <li><s>Generate / Load a keypair</s></li>
      <li>Test signing arbitrary data</li>
      <li>Sign a challenge from the API</li>
      <li>Actually upload a file!!!</li>
      <li>Implement user registration</li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'Home',
  components: {
  },

  async created() {
    console.log("Hi there!");

    const keyPair = await crypto.subtle.generateKey(
      {
        name: "RSA-PSS",
        modulusLength: 4096,
        publicExponent: new Uint8Array([1, 0, 1]),
        hash: "SHA-256",
      },

      false, // Don't allow this key to be exported

      ["sign", "verify"] // Permissions needed by this key
    );

    console.log(keyPair);
  },
}
</script>
