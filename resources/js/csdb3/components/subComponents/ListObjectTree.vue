<script>
import ListObjectTree from './ListObjectTree.vue';
import { useTechpubStore } from '../../../techpub/techpubStore';
export default {
  // components: { ListObjectTree },
  data() {
    return {
      marginLeft: 10,
      techpubStore: useTechpubStore(),
      open: {},
    }
  },
  props: {
    objects: Object,
    type: String,
    addedMargin: {
      type: Number,
      default: 5,
    },
  },
  methods: {
    clickFilename(model) {
      this.emitter.emit('openfile', { model: model });
    }
  },
}
</script>
<template>
  <div v-if="type" v-for="(obj, path) in techpubStore[`${$props.type}_list`]"
    :style="`margin-left:${marginLeft + $props.addedMargin}px`">
    <div v-if="path.substring(0, 2) === '__'" class="">
      <div class="details">
        <h5 class="summary hover:bg-blue-300"><button @click="open[path] = !open[path]"
            class="material-icons align-middle text-sm"> {{ open[path] ? 'expand_more' : 'chevron_right' }} </button> <a
            href="#" class="text-base focus:border-black">{{ path.substring(2) }}</a> </h5>
        <div v-show="open[path]">
          <ListObjectTree :objects="obj" />
        </div>
      </div>
    </div>
    <div v-else class="text-base ml-1 hover:bg-blue-300">
      <a href="#" @click.prevent="clickFilename(obj)" class="text-base focus:border-black">{{ obj.filename }}</a>
    </div>
  </div>

  <div v-else-if="objects" v-for="(obj, path) in objects" :style="`margin-left:${marginLeft + $props.addedMargin}px`">
    <div v-if="path.substring(0, 2) === '__'">
      <div class="details">
        <h5 class="summary hover:bg-blue-300"><button @click="open[path] = !open[path]"
            class="material-icons align-middle text-sm"> {{ open[path] ? 'expand_more' : 'chevron_right' }} </button> <a
            href="#" class="text-base focus:border-black">{{ path.substring(2) }}</a> </h5>
        <div v-show="open[path]">
          <ListObjectTree :objects="obj" />
        </div>
      </div>
    </div>
    <div v-else class="text-base ml-1 hover:bg-blue-300">
      <a href="#" @click.prevent="clickFilename(obj)" class="text-base focus:border-black">{{ obj.filename }}</a>
      {{ obj.initiator }}
      <s v-if="obj.editable">editable</s>
      <span v-else>editable</span>
    </div>
  </div>
</template>