import {
    user_init, static_user_application, static_user_chat_history, group_get_relation, util_upload_img, util_upload_file
} from "./api.js";
import {getCookie} from "./util.js";
import {ready, toMessage, toolCode, userSign, userStatus, videoRoom} from "./event.js";
//以下为 layim 最新版写法
layui.config({
    layimPath: '../../dist/' //配置 layim.js 所在目录
    ,layimAssetsPath: '../../dist/layim-assets/' //layim 资源文件所在目录
}).extend({
    layim: layui.cache.layimPath + 'layim' //配置 layim 组件所在的路径
}).use('layim', function(layim){ //加载组件
                                 //基础配置
    layim.config({
        init: {
            url: user_init,
            type: 'get',
            data: {
                token: getCookie('IM_TOKEN')
            },
        } //获取主面板列表信息，下文会做进一步介绍

        //获取群员接口（返回的数据格式见下文）
        ,members: {
            url: group_get_relation //接口地址（返回的数据格式见下文）
            ,type: 'get' //默认get，一般可不填
            ,data: {
                token: getCookie('IM_TOKEN')
            } //额外参数
        }

        //上传图片接口（返回的数据格式见下文），若不开启图片上传，剔除该项即可
        ,uploadImage: {
            url: util_upload_img + '?token=' + getCookie('IM_TOKEN') //接口地址
            ,type: 'post' //默认post
        }

        //上传文件接口（返回的数据格式见下文），若不开启文件上传，剔除该项即可
        ,uploadFile: {
            url: util_upload_file + '?token=' + getCookie('IM_TOKEN') //接口地址
            ,type: 'post' //默认post
        }
        //扩展工具栏，下文会做进一步介绍（如果无需扩展，剔除该项即可）
        ,tool: [{
            alias: 'code' //工具别名
            ,title: '代码' //工具名称
            ,icon: '' //工具图标，参考图标文档
        },{
            alias: 'video',
            title: '视频通话',
            icon: '&#xe643;'
        }]

        ,msgbox: layui.cache.dir + 'css/modules/layim/html/msgbox.html' //消息盒子页面地址，若不开启，剔除该项即可
        ,find: layui.cache.dir + 'css/modules/layim/html/find.html' //发现页面地址，若不开启，剔除该项即可
        ,chatLog: layui.cache.dir + 'css/modules/layim/html/chatlog.html' //聊天记录页面地址，若不开启，剔除该项即可
        ,brief: false // 是否简约模式，如果设为 true，则主面板不会显示
        ,title: "我的聊天" // 主面板最小化后显示的名称
        ,min: false // 用于设定主面板是否在页面打开时，始终最小化展现
        ,right: "0px" // 用于设定主面板右偏移量。该参数可避免遮盖你页面右下角已经的bar
        ,minRight: "200px" // 用户控制聊天面板最小化时、及新消息提示层的相对right的px坐标
        ,initSkin: "5.jpg" // 设置初始背景，默认不开启。可设置./css/modules/layim/skin目录下的图片文件名
        ,isAudio: false // 是否开启聊天工具栏音频
        ,isVideo: false // 是否开启开启聊天工具栏视频
        ,notice: false // 是否开启桌面消息提醒，即在浏览器之外的提醒
        ,voice: "default.mp3" // 设定消息提醒的声音文件（所在目录：./layui/css/modules/layim/voice/）
        ,isfriend: true // 是否开启好友
        ,isgroup: true // 是否开启群组
        ,maxLength: 3000 // 可允许的消息最大字符长度
        ,copyright: true
    });toolCode();
    videoRoom();
    ready();
    userStatus();
    userSign();
    toMessage();
});