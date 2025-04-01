
export default {
  bootstrap: () => import('./main.server.mjs').then(m => m.default),
  inlineCriticalCss: true,
  baseHref: '/',
  locale: undefined,
  routes: [
  {
    "renderMode": 2,
    "route": "/"
  }
],
  entryPointToBrowserMapping: undefined,
  assets: {
    'index-angular.html': {size: 496, hash: 'd0f826ab71cbfaf5e5a62d895dd4f6aebe7cbb9d8239fd5aeafb3d3e1f7e7eb6', text: () => import('./assets-chunks/index-angular_html.mjs').then(m => m.default)},
    'index.server.html': {size: 1009, hash: 'e837bb925ba7c754d7062baef5d34684046dd372a24a048460b9f3fe583b7a20', text: () => import('./assets-chunks/index_server_html.mjs').then(m => m.default)},
    'index.html': {size: 20841, hash: '103bb1079aee88885476187f3b3e8e32dc34a6d31b5767bb4680c9db875545cc', text: () => import('./assets-chunks/index_html.mjs').then(m => m.default)},
    'styles-5INURTSO.css': {size: 0, hash: 'menYUTfbRu8', text: () => import('./assets-chunks/styles-5INURTSO_css.mjs').then(m => m.default)}
  },
};
