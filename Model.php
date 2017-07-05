<?php
//1.构建Model类的命名空间
//2.便于通过命名空间准确的调用Model类中的方法
namespace zhangzhifeng1988\model;
//1.构建Model类
//2.通过调用Model类来完成静态调用或者普通调用类方法的都可以实现调用Base中的方法
class Model{
//      1.构建私有属性$config
//      2.$config用来存储setConfig方法时，从system/config/database.php传过来配置数据，用来连接数据库
    private static $config;
//      1.构建__call方法
//      2.当普通调用方法在当前页面没找到时自动触发此方法，执行里面的代码
    public function __call($name, $arguments)
    {
//        1.调用当前方法parseAction,
//        2.里面存着连接数据库的配置项
        return self::parseAction($name,$arguments);

    }
//      1.构建__callstatic方法
//      2.当静态调用方法在当前页面没找到时自动触发此方法，执行里面的代码
    public static function __callStatic($name, $arguments)
    {
//        1.调用当前方法parseAction,
//        2.里面存着连接数据库的配置项
        return self::parseAction($name,$arguments);
    }

//  【打包配置项，调用表格，信息】
//        1.构建parseAction方法
//        2.通过此方法把配置文件统一整理归到一块，无论__call还是__callStatic都可以直接调用，简单节省代码
    private  static function parseAction($name,$arguments){
            //system、model\Article
//              1.通过get_called_class函数获得调用当前函数的类名（命名空间）,
//              2.哪个类调用获得哪个类，这里获得system、model\Article
           $table=get_called_class();
//              1.从得到的命名空间类名中提取出表名，因为访问的数据库表名就是需要调用的数据库表的名字
//              2.通过以“\”为标准从右面截取字符串，再去掉“\”，得到完整的数据库表名，然后转小写，注意‘\’要转义
           $table=strtolower(ltrim(strrchr($table,'\\'),'\\'));
//            1.通过调用Base类中的方法来实现连接数据库和从数据库中提取信息
//            2.new Base，实例化Base类，进入Base数据库控管理页面，传入两个参数（当前的私有属性$config：存有数据库配置信息，$table:调用的表名）
//                $name 为要Entry类arc方法中调用的方法名例如：get(),find()....
           return call_user_func_array([new Base(self::$config,$table),$name],$arguments);
    }


//  【接收配置信息】

//        1.构建setConfig方法
//        2.此方法用来接收传递从配置文件system/config/database.php配置文件传进来的联系数据库配置信息，传递给私有属性$config
    public static function setConfig($config){
//        p($config);
//        1.调用私有属性$config赋值传进来的配置信息
//        2.通过把配置信息赋值给当前类的私有属性方便在当前类的其他方法中调用，能通过__call或__callstatic，传递给Base类进行连接数据库
        self::$config=$config;


    }



}