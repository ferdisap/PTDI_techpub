<script>
import BottomBar from '../subComponents/BottomBar.vue';
import ButtonSizingWidth from '../subComponents/ButtonSizingWidth.vue';
import LisTree from '../componentns/ListTree.vue'
import Folder from '../componentns/Folder.vue';
import { objectType } from '../../../helper.js';
import Preview from '../componentns/Preview.vue';
import IdentStatus from '../componentns/IdentStatus.vue';
export default {
  components: { BottomBar, ButtonSizingWidth, LisTree, Folder, Preview, IdentStatus },
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
          isShow: true,
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
        satu: { isMaximize: true, portion: 0.1 },
        dua: { isMaximize: true, portion: 0.675 },
        tiga: { isMaximize: false, portion: 0.225 },
      },

    }
  },
  computed: {
    col1Width() {
      return `width:${this.colWidth['satu']['portion'] * 100}%`;
    },
    col2Width() {
      return `width:${this.colWidth['dua']['portion'] * 100}%`;
    },
    col3Width() {
      return `width:${this.colWidth['tiga']['portion'] * 100}%`;
    }
  },
  methods: {
    sizing(colnum) {
      this.colWidth[colnum]['isMaximize'] = !this.colWidth[colnum]['isMaximize'];
      let p1 = this.colWidth['satu']['portion'];
      let p2 = this.colWidth['dua']['portion'];
      let p3 = this.colWidth['tiga']['portion'];
      const minimize = function (colnum = '') {
        if (colnum === 'satu') {
          let p1_n = p1 / 2;
          let terkurang = p1 - p1_n;
          p2 = p2 + (terkurang / 2);
          p3 = p3 + (terkurang / 2);
          return [p1_n, p2, p3]
        }
        else if (colnum === 'dua') {
          let p2_n = p2 / 2;
          p3 = 1 - p1 - p2_n
          return [p1, p2_n, p3]
        }
        else if (colnum === 'tiga') {
          let p3_n = p3 / 2;
          p2 = 1 - p1 - p3_n
          return [p1, p2, p3_n]
        }
      };
      const maximize = function (colnum = '') {
        if (colnum === 'satu') {
          let p1_n = p1 * 2;
          let tertambah = p1_n - p1;
          p2 = p2 - (tertambah / 2);
          p3 = p3 - (tertambah / 2);
          return [p1_n, p2, p3];
        }
        else if (colnum === 'dua') {
          let p2_n = p2 * 2;
          p3 = 1 - p1 - p2_n
          return [p1, p2_n, p3]
        }
        else if (colnum === 'tiga') {
          let p3_n = p3 * 2;
          p2 = 1 - p1 - p3_n
          return [p1, p2, p3_n]
        }
      }
      let portions = this.colWidth[colnum]['isMaximize'] ? (maximize.bind(this, colnum))() : (minimize.bind(this, colnum))();
      this.colWidth['satu']['portion'] = portions[0];
      this.colWidth['dua']['portion'] = portions[1];
      this.colWidth['tiga']['portion'] = portions[2];
    },
  },
  mounted(){
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
    });
    this.emitter.on('clickFolderFromListTree', (data) => {
      this.bottomBarItems.Folder.isShow = true;
      this.bottomBarItems.Folder.data = data; // hanya ada path saja di data
    });
  }
}
</script>
<template>
  <div class="explorer overflow-auto h-full">
    <!-- <div class="bg-white px-3 py-3 2xl:h-[90%] xl:h-[85%] lg:h-[80%] md:h-[75%] sm:h-[90%]"> -->
    <div class="bg-white px-3 py-3 2xl:h-[92%] xl:h-[90%] lg:h-[88%] md:h-[90%] sm:h-[90%] h-full">

      <div class="h-[10%]">
        <h1 class="text-blue-500">EXPLORER</h1>
        <hr class="border-2 border-blue-500" />
      </div>

      <div class="explorer-content flex h-[90%]">
        <!-- col 1 -->
        <div :class="['border-r-2 border-blue-500 pr-2 overflow-auto text-nowrap relative h-full',]" :style="[col1Width]">
          <ButtonSizingWidth :fn="sizing.bind(this, 'satu')" />
          <LisTree type="allobjects"/>
        </div>
        <!-- col 2 -->
        <div :class="['border-r-2 px-2 border-blue-500 pr-2 overflow-auto text-wrap relative h-full',]" :style="[col2Width]">
          <ButtonSizingWidth :fn="sizing.bind(this, 'dua')" />
          <Folder v-if="bottomBarItems.Folder.isShow" :data-props="bottomBarItems.Folder.data"/>
          <IdentStatus v-if="bottomBarItems.IdentStatus.isShow" :dataProps="bottomBarItems.IdentStatus.data"/>
        </div>
        <!-- col 3 -->
        <div :class="['border-r-2 border-blue-500 pr-2 overflow-auto text-wrap relative h-full',]" :style="[col3Width]">
          <ButtonSizingWidth :fn="sizing.bind(this, 'tiga')" />
          <Preview v-if="bottomBarItems.Preview.isShow" :dataProps="bottomBarItems.Preview.data"/>
        </div>
      </div>

    </div>

    <div class="w-full relative flex justify-center 2xl:h-[8%] xl:h-[10%] lg:h-[12%] md:h-[10%] sm:h-[10%]">
      <BottomBar :items="bottomBarItems" class="" />
    </div>
  </div>
</template>