import { formDataToObject } from './helper';
import routes from '../../others/routes.json';

class RoutesWeb {
  // static #WebRoutes = routes;

  static get(name, config = {}){
    const route = Object.assign({}, routes[name]);

    // set route params
    if(config.params && isObject(config.params)){      
      if(config.params.constructor.name === 'FormData') config.params = formDataToObject(data);
      Object.keys(config.params).forEach(k => {
        route.path = route.path.replace(new RegExp(`:${k}\\??`), config.params[k]);
      });
      delete config.params;
    }

    // clear the path
    route.path = route.path.replace(/(:\/\/)|(\/)+/g, "$1$2"); // untuk menghilangkan multiple slash
    
    // set route url and data
    if (!route.method || route.method.includes('GET')) {
      const url = new URL(window.location.origin + route.path);
      url.search = new URLSearchParams(config.query);
      route.url = url.toString();
      route.data = Object.assign({}, route.data); // supaya tidak ada Proxy
    }
    else if(route.method.includes('POST')){
      route.data = config.data;
      route.url = new URL(window.location.origin + route.path).toString();
    }

    route.method = Object.assign({}, route.method); // supaya tidak ada Proxy, sehingga worker bisa pakainya
    return route;
  }
}

export default RoutesWeb;