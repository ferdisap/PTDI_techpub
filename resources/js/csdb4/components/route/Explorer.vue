<script>
import BottomBar from '../subComponents/BottomBar.vue';
import LisTree from '../componentns/ListTree.vue'
import Folder from '../componentns/Folder.vue';
import { objectType } from '../../../helper.js';
import Preview from '../componentns/Preview.vue';
import IdentStatus from '../componentns/IdentStatus.vue';
import Editor from '../componentns/Editor.vue';
export default {
  components: { BottomBar, LisTree, Folder, Preview, IdentStatus, Editor },
  data() {
    return {
      bottomBarItems: {
        Folder: {
          iconName: 'folder',
          tooltipName: 'Folder',
          isShow: false,
          data: {},
          type: undefined,
        },
        IdentStatus: {
          iconName: 'badge',
          tooltipName: 'Ident-Status',
          isShow: false,
        },
        Analyzer: {
          iconName: 'pie_chart',
          tooltipName: 'Anlayzer',
          isShow: true,
        },
        Editor: {
          iconName: 'ink_pen',
          tooltipName: 'Editor',
          isShow: false,
        },
        History: {
          iconName: 'history_edu',
          tooltipName: 'History',
          isShow: true,
        },
        Preview: {
          iconName: 'preview',
          tooltipName: 'Preview',
          isShow: false,
          data: {}
        },
      },
      colWidth: {
        satu: { portion: 0.15 },
        dua: { portion: 0.5 }, // 0.5 karena col2 dan col3 ada pada satu div yang sama biar memudahkan resizing
        tiga: { portion: 0.5 }, // 0.5 karena col2 dan col3 ada pada satu div yang sama biar memudahkan resizing
      },

    }
  },
  computed: {
    col1Width() {
      return `width:${this.colWidth['satu']['portion'] * 100}%`;
    },
    col23ContainerWidth(){
      return `width:${(1 - this.colWidth['satu']['portion']) * 100}%`;
    },
    col2Width() {
      return `width:${this.colWidth['dua']['portion'] * 100}%`;
      this.colWidth['dua']['portion'] = (1 - this.colWidth['tiga']['portion'] - this.colWidth['satu']['portion']);
      return `width:${this.colWidth['dua']['portion'] * 100}%`;
    },
    col3Width() {
      return `width:${this.colWidth['tiga']['portion'] * 100}%`;
      this.colWidth['tiga']['portion'] = (1 - this.colWidth['dua']['portion'] - this.colWidth['satu']['portion']);
      return `width:${this.colWidth['tiga']['portion'] * 100}%`;
      // console.log(portion);
      // console.log('aa');
      // return `width:${(1 - this.colWidth['dua']['portion'] - this.colWidth['satu']['portion']) * 100}%`;
      // this.colWidth['tiga']['portion'] = (1 - this.colWidth['dua']['portion'] - this.colWidth['satu']['portion']);
    }
  },
  methods: {
    /*
     * double click if you want to terminate resizing, or keep quiet for one second
    */
    sizing2(event, colnum) {
      // let ex = event.target.getBoundingClientRect().left; // 272.466
      // let ewidth = event.target.getBoundingClientRect().width; // 3.5714
      let parentLeft = event.target.parentElement.getBoundingClientRect().left; //86.741
      let parentWidth = event.target.parentElement.getBoundingClientRect().width; // 189.296
      let acuanWidth = document.querySelector('.explorer-content').getBoundingClientRect().width // 1261.997802734375
      // parentLeft + parentWidth - ex - ewidth // -0.000002384185791015625
      let startEventXPortion = parentWidth / acuanWidth // 0.1499977849326281
      this.dt = 1000;
      const resize = (e) => {
        if (this.dt) {
          clearTimeout(this.to);
          let endEventXPortion = (e.clientX - parentLeft - parentWidth) / acuanWidth;
          this.colWidth[colnum].portion = startEventXPortion + endEventXPortion;
          this.to = setTimeout(() => document.removeEventListener('mousemove', resize, false), this.dt);
          if (colnum === 'dua') {
            this.colWidth['tiga']['portion'] = 1 - this.colWidth['dua']['portion'];
          } else if(colnum === 'satu'){
            // this.colWidth['tiga']['portion'] = 1 - this.colWidth['dua']['portion'];
          }
        }
        window.localStorage.setItem('colWidthExplorer', JSON.stringify(this.colWidth));
      }
      document.addEventListener('mousemove', resize, false);
    }
  },
  mounted() {
    document.addEventListener('dblclick', () => this.dt = 0);
    if(window.localStorage.colWidthExplorer){
      this.colWidth = JSON.parse(window.localStorage.colWidthExplorer);
    }

    this.emitter.on('clickFilenameFromListTree', (data) => {
      // Folder
      this.bottomBarItems.Folder.data = data; // hanya ada filename dan path di data
      // this.bottomBarItems.Folder.isShow = true; // hanya dumping

      // identStatus
      this.bottomBarItems.IdentStatus.isShow = true;
      this.bottomBarItems.IdentStatus.data = data; // hanya ada filename dan path di data

      // Preview
      this.bottomBarItems.Preview.isShow = true;
      this.bottomBarItems.Preview.data = data; // hanya ada filename dan path di data

      // Editor
      this.bottomBarItems.Editor.data = data; // hanya ada path saja di data
      // console.log(data);
    });

    this.emitter.on('clickFolderFromListTree', (data) => {
      // Folder
      this.bottomBarItems.Folder.isShow = true;
      this.bottomBarItems.Folder.data = data; // hanya ada path saja di data
    });

    this.emitter.on('clickFilenameFromFolder', (data) => {
      // identStatus
      this.bottomBarItems.IdentStatus.data = data; // hanya ada filename dan path di data

      // Preview
      this.bottomBarItems.Preview.isShow = true;
      this.bottomBarItems.Preview.data = data; // hanya ada filename dan path di data

      // Editor
      this.bottomBarItems.Editor.data = data; // hanya ada filename dan path di data
      // console.log(data);
    });
  }
}
</script>
<template>
  <div class="explorer overflow-auto h-full">
    <!-- <div class="bg-white px-3 py-3 2xl:h-[90%] xl:h-[85%] lg:h-[80%] md:h-[75%] sm:h-[90%]"> -->
    <div class="bg-white px-3 py-3 2xl:h-[92%] xl:h-[90%] lg:h-[88%] md:h-[90%] sm:h-[90%] h-full">

      <div class="2xl:h-[5%] xl:h-[6%] lg:h-[8%] md:h-[9%] sm:h-[11%]">
        <h1 class="text-blue-500">EXPLORER</h1>
        <hr class="border-2 border-blue-500" />
      </div>

      <!-- <div class="explorer-content flex 2xl:h-[96%] xl:h-[97%] lg:h-[96%] md:h-[94%] sm:h-[95%]"> -->
      <div class="explorer-content flex 2xl:h-[95%] xl:h-[94%] lg:h-[92%] md:h-[91%] sm:h-[89%]">
        <!-- col 1 -->
        <div class="flex" :style="[col1Width]">
          <div class="overflow-auto text-nowrap relative h-full w-full">
            <LisTree type="allobjects" />
          </div>
          <div class="v-line h-full border-l-4 border-blue-500 cursor-ew-resize"
            @mousedown.prevent="sizing2($event, 'satu')"></div>
        </div>

        <div class="flex" :style="[col23ContainerWidth]">
          <!-- col 2 -->
          <div class="flex" :style="[col2Width]">
            <div class="overflow-auto text-wrap relative h-full w-full">
              <Folder v-if="bottomBarItems.Folder.isShow" :data-props="bottomBarItems.Folder.data" />
              <IdentStatus v-if="bottomBarItems.IdentStatus.isShow" :dataProps="bottomBarItems.IdentStatus.data" />
              <Editor v-if="bottomBarItems.Editor.isShow" :filename="bottomBarItems.Editor.data ? bottomBarItems.Editor.data.filename : ''" text="" />
            </div>
          </div>
          <div class="v-line h-full border-l-[4px] border-blue-500 w-0 cursor-ew-resize"@mousedown.prevent="sizing2($event, 'dua')"></div>

          <!-- col 3 -->
          <div class="flex" :style="[col3Width]">
            <div class="overflow-auto text-wrap relative h-full w-full">
              <Preview v-if="bottomBarItems.Preview.isShow" :dataProps="bottomBarItems.Preview.data" />
            </div>
          </div>

          <div class="v-line h-full border-l-4 border-blue-500 w-0"></div>
        </div>
      </div>

    </div>

    <div class="w-full relative flex justify-center 2xl:h-[8%] xl:h-[10%] lg:h-[12%] md:h-[10%] sm:h-[10%]">
      <BottomBar :items="bottomBarItems" class="" />
    </div>
  </div>
</template>