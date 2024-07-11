<script>
import BottomBar from '../subComponents/BottomBar.vue';
import EditorDML from '../componentns/EditorDML.vue';
import ListTree from '../componentns/ListTree.vue';
import PreviewComment from '../componentns/PreviewComment.vue';
export default {
  name: 'ManagementData',
  components:{
    BottomBar, EditorDML, ListTree, PreviewComment
  },
  data(){
      return {
        bottomBarItems: {
          EditorDML: {
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
          PreviewComment: {
            iconName: 'comment',
            tooltipName: 'Comment',
            isShow: false,
            data: {}
          },
        },
        colWidth: {
          satu: { portion: 0.15 },
          dua: { portion: 0.5 }, // 0.5 karena col2 dan col3 ada pada satu div yang sama biar memudahkan resizing
          tiga: { portion: 0.5 }, // 0.5 karena col2 dan col3 ada pada satu div yang sama biar memudahkan resizing
        },
      };
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
        let acuanWidth = document.querySelector('.managementList-content').getBoundingClientRect().width // 1261.997802734375
        // parentLeft + parentWidth - ex - ewidth // -0.000002384185791015625
        let startEventXPortion = parentWidth / acuanWidth // 0.1499977849326281
        let sizing = (e) => {
          let endEventXPortion = (e.clientX - parentLeft - parentWidth) / acuanWidth;
          this.colWidth[colnum].portion = startEventXPortion + endEventXPortion;
          if (colnum === 'dua') {
            this.colWidth['tiga']['portion'] = 1 - this.colWidth['dua']['portion'];
          }
          top.localStorage.setItem('colWidthManagemenData', JSON.stringify(this.colWidth));
        }
        document.addEventListener('mousemove', sizing);
        document.addEventListener('mouseup', this.turnOffSizing.bind(this, sizing), {once:true});
      },
      turnOffSizing(callback){
        document.removeEventListener('mousemove', callback, false)
      },
      tesListtree(){
        this.emitter.emit('tesListtree');
      }
    },
    mounted() {
      top.tes = this.tesListtree;
      if(top.localStorage.colWidthManagemenData){
        this.colWidth = JSON.parse(top.localStorage.colWidthManagemenData);
      }
      
      this.emitter.on('add_comment', () => {
        this.bottomBarItems.PreviewComment.isShow = true;
      })

      this.emitter.on('clickFilenameFromListTree', (data) => {
        this.bottomBarItems.EditorDML.isShow = true;
        this.bottomBarItems.EditorDML.data = data; // hanya ada filename dan path saja di data
      });
    }
  }
</script>
<template>
  <div class="management overflow-auto h-full">
    <div class="bg-white px-3 py-3 2xl:h-[92%] xl:h-[90%] lg:h-[88%] md:h-[90%] sm:h-[90%] h-full">

      <div class="2xl:h-[5%] xl:h-[6%] lg:h-[8%] md:h-[9%] sm:h-[11%] border-b-4 border-blue-500 grid items-center">
        <h1 class="text-blue-500">DATA MANAGEMENT</h1>
      </div>

      <div class="managementList-content flex 2xl:h-[95%] xl:h-[94%] lg:h-[92%] md:h-[91%] sm:h-[89%]">
          <!-- col 1 -->
          <div class="flex" :style="[col1Width]">
            <div class="overflow-auto text-nowrap relative h-full w-full">
              <ListTree type="dmrl" routeName="ManagementData"/>
            </div>
            <div class="v-line h-full border-l-4 border-blue-500 cursor-ew-resize" @mousedown.prevent="turnOnSizing($event, 'satu')"></div>
          </div>

        <!-- col 2 -->
        <div class="flex" :style="[col2Width]">
          <div class="overflow-auto text-wrap relative h-full w-full">
            <EditorDML v-if="bottomBarItems.EditorDML.isShow" :filename="bottomBarItems.EditorDML.data.filename" text="" />
          </div>
        </div>
        <div class="v-line h-full border-l-[4px] border-blue-500 w-0 cursor-ew-resize" @mousedown.prevent="turnOnSizing($event, 'dua')"></div>

        <!-- col 3 -->
        <div class="flex" :style="[col3Width]">
          <div class="overflow-auto text-wrap relative h-full w-full">
            <PreviewComment v-if="bottomBarItems.PreviewComment.isShow" :filename="bottomBarItems.PreviewComment.data.filename"/>
          </div>
        </div>  
      </div>

      <div class="w-full relative flex justify-center 2xl:h-[8%] xl:h-[10%] lg:h-[12%] md:h-[10%] sm:h-[10%]">
        <BottomBar :items="bottomBarItems" class="" />
      </div>
      
    </div>
  </div>
</template>