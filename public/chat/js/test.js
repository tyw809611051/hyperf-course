//以下为 layim 最新版写法
layui.config({
    layimPath: '../../layim/dist/' //配置 layim.js 所在目录
    ,layimAssetsPath: '../../layim/dist/layim-assets/' //layim 资源文件所在目录
}).extend({
    layim: layui.cache.layimPath + 'layim' //配置 layim 组件所在的路径
}).use('layim', function(layim){ //加载组件
                                 //先来个客服模式压压精
    layim.config({
        brief: true //是否简约模式（如果true则不显示主面板）
    }).chat({
        name: '一个新窗口'
        ,type: 'friend'
        ,avatar: 'http://tp1.sinaimg.cn/5619439268/180/40030060651/1'
        ,id: -2
    });
});