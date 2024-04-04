<script>
import BottomBar from '../subComponents/BottomBar.vue';
import ListTree from '../componentns/ListTree.vue'
import Folder from '../componentns/Folder.vue';
import { objectType } from '../../../helper.js';
import Preview from '../componentns/Preview.vue';
import IdentStatus from '../componentns/IdentStatus.vue';
import Editor from '../componentns/Editor.vue';
import History from '../componentns/History.vue';
import Option from '../componentns/Option.vue';
export default {
  components: { BottomBar, ListTree, Folder, Preview, IdentStatus, Editor, History, Option},
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
          isShow: false,
        },
        Editor: {
          iconName: 'ink_pen',
          tooltipName: 'Editor',
          isShow: false,
          data: {}
        },
        History: {
          iconName: 'history_edu',
          tooltipName: 'History',
          isShow: false,
          data: {},
        },
        Preview: {
          iconName: 'preview',
          tooltipName: 'Preview',
          isShow: false,
          data: {}
        },
        Option: {
          iconName: 'settings',
          tooltipName: 'Option',
          isShow: false,
          data:{},
        }
        ,
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
    col2Width() {
      this.colWidth['dua']['portion'] = (1 - this.colWidth['satu']['portion'] - this.colWidth['tiga']['portion']);
      return `width:${this.colWidth['dua']['portion'] * 100}%`;
    },
    col3Width() {
      this.colWidth['tiga']['portion'] = (1 - this.colWidth['satu']['portion'] - this.colWidth['dua']['portion']);
      return `width:${this.colWidth['tiga']['portion'] * 100}%`;
    }
  },
  methods: {
    turnOnSizing(event, colnum) {
      // let ex = event.target.getBoundingClientRect().left; // 272.466
      // let ewidth = event.target.getBoundingClientRect().width; // 3.5714
      let parentLeft = event.target.parentElement.getBoundingClientRect().left; //86.741
      let parentWidth = event.target.parentElement.getBoundingClientRect().width; // 189.296
      let acuanWidth = document.querySelector('.explorer-content').getBoundingClientRect().width // 1261.997802734375
      // parentLeft + parentWidth - ex - ewidth // -0.000002384185791015625
      let startEventXPortion = parentWidth / acuanWidth // 0.1499977849326281
      let sizing = (e) => {
        let endEventXPortion = (e.clientX - parentLeft - parentWidth) / acuanWidth;
        this.colWidth[colnum].portion = startEventXPortion + endEventXPortion;
        if (colnum === 'dua') {
          this.colWidth['tiga']['portion'] = 1 - this.colWidth['dua']['portion'];
        }
        top.localStorage.setItem('colWidthExplorer', JSON.stringify(this.colWidth));
      }
      document.addEventListener('mousemove', sizing);
      document.addEventListener('mouseup', this.turnOffSizing.bind(this, sizing), {once:true});
    },
    turnOffSizing(callback){
      document.removeEventListener('mousemove', callback, false)
    }
  },
  mounted() {
    if(window.localStorage.colWidthExplorer){
      this.colWidth = JSON.parse(window.localStorage.colWidthExplorer);
    }

    this.emitter.on('clickFilenameFromListTree', (data) => {
      // Folder
      this.bottomBarItems.Folder.data = data; // hanya ada filename dan path di data
      // this.bottomBarItems.Folder.isShow = true; // hanya dumping

      // identStatus
      this.bottomBarItems.IdentStatus.isShow = data.filename.slice(0,3) === 'ICN' ? false : true;
      this.bottomBarItems.IdentStatus.data = data; // hanya ada filename dan path di data

      // Preview
      this.bottomBarItems.Preview.isShow = true;
      setTimeout(()=>{
        this.emitter.emit('Preview-refresh', data); // hanya ada filename dan path di data
      },0);

      // Editor
      this.bottomBarItems.Editor.data = data; // hanya ada filename dan path saja di data
      
      // History
      this.bottomBarItems.History.data = data; // hanya ada filename dan path saja di data

      // Option
      this.bottomBarItems.Option.data = data; // hanya ada filename dan path saja di data
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
      
      // History
      this.bottomBarItems.History.data = data; // hanya ada filename dan path di data

      // Option
      this.bottomBarItems.Option.data = data; // hanya ada filename dan path di data
    });

    this.emitter.on('createObjectFromEditor', (data) => { 
      // data hanya mengandung model. data.model = {}
      // kemudian model di push ke Listtree object untuk di update listtree nya
      // kemudian preview akan reload sesuai dengan model tersebut
      // kemudian history di reload sesuai model
      // kemudian Ident status di reload sesuai model
      // kemudian Folder di reload sesuai path model. Tapi ini kayaknya tidak perlu. Bikin ribet saja
      // item bottomBar yang lain di set false (hide)
      // alert('emitting to refresh list tree');
      this.emitter.emit('ListTree-add', data.model);
      this.bottomBarItems.IdentStatus.data = data.model;
      this.bottomBarItems.Preview.isShow = true;
      this.bottomBarItems.Preview.data = data.model;
      this.bottomBarItems.IdentStatus.data = data.model;
      this.bottomBarItems.History.data = data.model;
      this.bottomBarItems.Option.data = data.model;
    })

    this.emitter.on('createICNFromEditor', (data) => {
      this.emitter.emit('ListTree-add', data.model);
    });

    this.emitter.on('updateICNFromEditor', (data) => {
      console.log('explorer emitted by editor updateICNFromEditor');
      this.emitter.emit('Preview-refresh', data.model)
    });

    

    this.emitter.on('updateObjectFromEditor', (data) => {
      // data berupa model
      // history, identStatus di set false (hide) agar tidak memberatkan saat live preview
      // preview refresh view
      this.emitter.emit('Preview-refresh', data.model);
      this.emitter.emit('IdentStatus-refresh', data.model);
      // this.emitter.on('History-refresh', data.model); // sepertinya ini tidak usah. Biar ga kebanyakan request
    });

    this.emitter.on('readFileURLFromEditor', (data) => { // data berisi mime, source, sourceType
      this.bottomBarItems.Preview.isShow = true;
      setTimeout(() => {
        this.emitter.emit('Preview-refresh', data);
      },0);
      this.bottomBarItems.IdentStatus.isShow = false;
      this.bottomBarItems.History.isShow = false;
      this.bottomBarItems.Analyzer.isShow = false;
    });

    this.emitter.on('readTextFileFromEditor', () => {
      this.bottomBarItems.Preview.isShow = false;
      this.bottomBarItems.IdentStatus.isShow = false;
      this.bottomBarItems.History.isShow = false;
      this.bottomBarItems.Analyzer.isShow = false;
    });

    this.emitter.on('ChangePathCSDBObjectFromOption', (data) => {
      // data adalah model SQL CSDB Object
      this.emitter.emit('Folder-refresh', data);
      this.emitter.emit('ListTree-refresh', data); // tidak perlu kirim data karena nanti request ke server
      // console.log(data);
      // this.emitter.emit('Folder-updateModel', data);
    })

    this.emitter.on('DeleteCSDBObjectFromOption', (data) => {
      // data adalah array. data[0] adalah model SQL CSDB Object
      // data adalah array. data[1] adalah model Deletion Object
      this.emitter.emit('ListTree-remove', data[0]);
      this.emitter.emit('Deletion-refresh', data[1]);
    })

    this.emitter.on('RestoreCSDBobejctFromDeletion', (data) => {
      // data adalah model SQL CSDB Object
      this.emitter.emit('ListTree-add', data);
    })
  }
}
</script>
<template>
  <div class="explorer overflow-auto h-full">
    <div class="bg-white px-3 py-3 2xl:h-[92%] xl:h-[90%] lg:h-[88%] md:h-[90%] sm:h-[90%] h-full">

      <div class="2xl:h-[5%] xl:h-[6%] lg:h-[8%] md:h-[9%] sm:h-[11%] border-b-4 border-blue-500 grid items-center">
        <h1 class="text-blue-500">EXPLORER</h1>
      </div>

      <div class="explorer-content flex 2xl:h-[95%] xl:h-[94%] lg:h-[92%] md:h-[91%] sm:h-[89%]">
        <!-- col 1 -->
        <div class="flex" :style="[col1Width]">
          <div class="overflow-auto text-nowrap relative h-full w-full">
            <ListTree type="allobjects" />
          </div>
          <div class="v-line h-full border-l-4 border-blue-500 cursor-ew-resize" @mousedown.prevent="turnOnSizing($event, 'satu')"></div>
        </div>

        <!-- col 2 -->
        <div class="flex" :style="[col2Width]">
          <div class="overflow-auto text-wrap relative h-full w-full">
            <Folder v-if="bottomBarItems.Folder.isShow" :data-props="bottomBarItems.Folder.data" />
            <IdentStatus v-if="bottomBarItems.IdentStatus.isShow" :dataProps="bottomBarItems.IdentStatus.data" />
            <Editor v-if="bottomBarItems.Editor.isShow" :filename="bottomBarItems.Editor.data.filename" text="" />
            <History v-if="bottomBarItems.History.isShow" :filename="bottomBarItems.History.data.filename"/>
          </div>
        </div>
        <div class="v-line h-full border-l-[4px] border-blue-500 w-0 cursor-ew-resize" @mousedown.prevent="turnOnSizing($event, 'dua')"></div>

        <!-- col 3 -->
        <div class="flex" :style="[col3Width]">
          <div class="overflow-auto text-wrap relative h-full w-full">
            <Option v-if="bottomBarItems.Option.isShow" :dataProps="bottomBarItems.Option.data"/>
            <Preview v-if="bottomBarItems.Preview.isShow" :dataProps="bottomBarItems.Preview.data" />
          </div>
        </div>
      </div>

    </div>

    <div class="w-full relative flex justify-center 2xl:h-[8%] xl:h-[10%] lg:h-[12%] md:h-[10%] sm:h-[10%]">
      <BottomBar :items="bottomBarItems" class="" />
    </div>
  </div>
</template>