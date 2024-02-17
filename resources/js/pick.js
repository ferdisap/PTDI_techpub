import axios from "axios";

let count = 0;

function pick(data = {}){
  const run = function(data){
    let id = (Math.random() + 1).toString(36).substring(7);
    axios.interceptors.response.use(
      (response) => {
        count++;
        console.log(id, count);
        id = '';
        return response;
      },
    );

    return {
      prop1: 'foo',
      data: data,
      id: id,

      /**
       * @return axios Promise
       */
      go(){       
        return axios.get('/');
        // return new Promise(resolve => {
        //   setTimeout(() => {
        //     resolve(1);
        //   },1000)
        // })
      }
    }
  };
  return run();
}

export default pick;

// function createAlert(conf = {}){
//   let aa = function(){
//     // selector = conf.selector ?? undefined;
//     message = conf.message ?? '';
//     alert = run();
//     return {
//       show: show,
//       message: message,
//       button: function(state){
//         if(state){
//           this.show = false;
//           return alert.ok();
//         } else {
//           this.show = false;
//           return alert.not_ok();
//         }
//       },
//       result: alert.wait(),
//     }
//   }
//   return aa();
// }