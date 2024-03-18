<script>
import Loading from './components/Loading.vue';
import Topbar from './components/Topbar.vue';
import { useTechpubStore } from './techpubStore';
export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      bannerSrc: "images/n219_images/01.jpg",
      bannerNo: 1,
    }
  },
  components:{
    Loading, Topbar
  },
  methods:{
    rollBanner(no = undefined){
      if(no){
        this.bannerNo = no
      }
      let max = 8;
      let min = this.bannerNo;

      const run = (recFn) => {
        min = (min >= max ? 1 : min);
        setTimeout(() => {
          this.bannerSrc = `images/n219_images/0${min}.jpg`;
          min++;
          this.bannerNo = min;
          no ? '' : recFn(recFn);
        }, no ? 0 : 10000);
      }
      run(run);
    },
  },
  mounted(){
    this.rollBanner();
    // document.body.classList.add('font-tahoma');
  }
}
</script>


<template>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  
  <!-- <Loading/> -->
  <div class="topbar w-full h-[50px]">
    <Topbar />
  </div>
  <Info :isSuccess="techpubStore.isSuccess" :errors="techpubStore.errors" :message="techpubStore.message"/>
  
  <!-- main -->
  <div class="relative">
    <div @click="rollBanner(bannerNo-1)" class="material-symbols-outlined ml-10 hover:outline-double text-black hover:cursor-pointer absolute top-1/2 left-0 text-4xl shadow-lg">arrow_back_ios</div>
    <div class="flex justify-center">
      <div class="w-[1000px]">
        <img :src="bannerSrc" style="width:100%;height:300px;object-fit:none"/>
        <div class="flex justify-center bg-black text-white">- PT Dirgantara Indonesia -</div>
      </div>
    </div>
    <div @click="rollBanner(bannerNo+1)" class="material-symbols-outlined mr-10 hover:outline-double text-black hover:cursor-pointer absolute top-1/2 right-0 text-4xl shadow-lg">arrow_forward_ios</div>
  </div>

  <!-- section -->
  <!-- our products -->
  <div class="mt-5">
    <div class="flex justify-center">
      <h1 class="text-5xl w-80 text-center">
        Our <span class="text-blue-500 text-5xl">Products</span>
        <hr class="mt-3 border-black border-2"/>
      </h1>
    </div>
    
    <div class="flex justify-center mt-10 rounded-lg">
      <div class="w-72 h-92 p-5 border mx-3">
        <h2 class="text-7xl text-center mt-3 mb-5">BRDP</h2>
        <p class="text-2xl mt-3">Business Rule Decision Point</p>
        <p class="mt-3 font-light text-justify">The rules must be implemented in each publication data authored.</p>
        <br/>
        <a href="#" class="button-primary mt-3">Details</a>
      </div>
      <div class="w-72 h-92 p-5 border mx-3">
        <h2 class="text-7xl text-center mt-3 mb-5">BREX</h2>
        <p class="text-2xl mt-3">Business Rule Exchange Index</p>
        <p class="mt-3 font-light text-justify">The summary of rules for validation of each realising publication.</p>
        <br/>
        <a href="#" class="button-primary mt-3">Details</a>
      </div>
      <div class="w-72 h-92 p-5 border mx-3">
        <h2 class="text-7xl text-center mt-3 mb-5">DML</h2>
        <p class="text-2xl mt-3">Business Rule Exchange Index</p>
        <p class="mt-3 font-light text-justify">Manage all data modules required to grouped by each publication type.</p>
        <br/>
        <a href="#" class="button-primary mt-3">Details</a>
      </div>
    </div>
  </div>

  <!-- news -->
  <div class="mt-5 px-[10%]">
    <div class="flex justify-center">
      <h1 class="text-5xl w-80 text-center">
        News <span class="text-blue-500 text-5xl">Update</span>
        <hr class="mt-3 border-black border-2"/>
      </h1>
    </div>

    <div class="mt-5 font-light">
      <h2 class="mb-3 text-4xl font-normal">New DMC has been released, General - Intercommunication System</h2>
      <span class="italic">2023 june 07</span>
      <p class="lead">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis at eligendi iusto commodi ad tempore placeat sapiente error quisquam autem!</p>
      <p>Read the <a href="#">details.</a></p>
    </div>

    <div class="mt-5 font-light">
      <h2 class="mb-3 text-4xl font-normal">New DMC has been released, General - Intercommunication System</h2>
      <span class="italic">2023 june 07</span>
      <p class="lead">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis at eligendi iusto commodi ad tempore placeat sapiente error quisquam autem!</p>
      <p>Read the <a href="#">details.</a></p>
    </div>
    
  </div>
  
  
</template>