<script>
export default {
  data() {
    return {
      // model: {},
    }
  },
  props:{
    objectsToDispatch:{
      type: Array,
      default: [],
    }
  },
  methods: {
    async getDDNList() {
      this.showLoadingProgress = true;
      let response = await axios({
        route: {
          name: 'api.get_ddn_list',
        },
        useMainLoadingBar: false,
      });

      if (response.statusText === 'OK') {
        // do something here
        // this.model = response.data.model;
      }
      this.showLoadingProgress = false;
    },
    async submit(event){
      this.showLoadingProgress = true;
      let fd = new FormData(event.target);

      let response = await axios({
        route: {
          name: 'api.create_ddn',
          data: fd,
        },
        useMainLoadingBar: false,
      });

      if (response.statusText === 'OK') {
        // do something here
        // this.model = response.data.model;
      }
      this.showLoadingProgress = false;
    }
  },
  mounted() {
    this.getDDNList();
  }
}
</script>
<template>
  <div class="Preview overflow-auto h-[93%] w-full relative px-3">

    <h1 class="h-[5%] mb-3 text-blue-500 w-full text-center">Dispatch To</h1>

    <!-- Table of Dispatch From -->
    <table class="text-left mb-3 h-[150px]" v-if="true">
      <thead class="text-sm">
        <tr class="leading-3 text-sm">
          <th>Name</th>
          <th>From</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>foo</td>
          <td>bar</td>
          <td>baz</td>
        </tr>
      </tbody>
    </table>

    <form @click.prevent="submit($event)">
      <div class="mb-8">
        <h2>
          <label class="text-3xl" for="dml_filename">DML Filename</label>
        </h2>
        <input class="mr-2 w-full" name="dml_filename" id="dml_filename" placeholder="eg.: DML-...xml"/>
      </div>
      <!-- <input name="languageIsoCode" id="languageIsoCode" placeholder="eg:. EN" class="mr-2 w-20" value="en"/> -->
      <!-- List of filename to dispatch -->
      <div class="mb-3">
        <h2 class="text-left text-3xl">Dispatched Object</h2>
        <table class="text-left h-[150px]">
          <thead>
            <tr>
              <td>No</td>
              <td>Filename</td>
              <td>Action</td>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(model, i) in $props.objectsToDispatch">
              <td>{{ i+1 }}</td>
              <td>{{ model.filename }}</td>
              <td>baz</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="w-full text-center">
        <button type="submit" name="button" class="button bg-violet-400 text-white hover:bg-violet-600">Submit</button>
      </div>

    </form>
  </div>
</template>