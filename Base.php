<?php
//1.构建Base类的命名空间
//2.便于通过命名空间精准调用Base类中的方法
namespace houdunwang\model;
//1.构建PDOException错误信息命名空间
//2.便于pdo错误信息被catch接收到输出
use PDOException;
//1.构建PDO变为全局变量
//2.数据库信息可以在本页面中被调用，便于连接数据库把数据连接信息存为静态，下次登录就不用连接了
use PDO;

//1.构建Base类
//2.通过Base类控制数据库信息
class Base{
//    1.构建私有静态属性 $pdo，，默认为空
//    2.$ pdo来存储数据库信息
    private static $pdo=NULL;
//    1.构建属性table,
//    2用来存储从Model页中获得传递进来的表名
    private $table;
//    1.构建$where属性
//    2.用来存储mysql中执行代码的条件where信息mysql语句
    private $where='';
//    1.构建自动载入函数,，当Base方法运行时先执行其中的代码
//    2.通过先执行其中的代码，在houdunwang/model/Model类中经过__call或__callstatic调用Base类时new Base（传入参数），被__construct接收，先首先要赋值给Base类的私有属性，便于被调用
    public function __construct($config,$table){
//        1.调用当前类的connect方法，把得到的$config穿参进去
//        2.connect方法为连接数据库的方法，通过把的到的配置项信息传进去，完成数据库连接，
        $this->connect($config);
//        1.给私有属性$table赋值
//        2.把传参过来得到的数据库表名赋值给私有属性$table，便于当前页面调用
        $this->table=$table;
//        p($this->table);

    }

/**
 * @连接数据库
 */

//      1.构建连接数据库方法
//      2.通过此方法直接连接到数据库信息，通过__construct()调用，直接自动连接到数据库，方便Base类中其他方法的运行
    private function connect($config){

//            1.判断如果当前$pdo是否为空
//            2.因为数据库的连接信息存在静态属性$pdo中，如果$pdo不为空，说明数据库已连接，直接返回连接，不用运行登录过程代码
            if(!is_null(self::$pdo)) return;
//            1.连接数据库
//            2.通过catch{},可以接收到在try{}中连接mysql时出现的错误信息输出出来，便于查找万一出现问题
            try{
//                1.组成了连接mysql语句连接数据库$dsn信息
//                2.通过传过来的配置信息$config中提取相对应的$config['db_host']和$config['db_name']信息
                $dsn = "mysql:host=" . $config['db_host'] . ";dbname=" . $config['db_name'];
//                1.组成了连接mysql语句连接数据库$dsn信息
//                2.通过传过来的配置信息$config中提取相对应的$config['db_user']信息
                $user = $config['db_user'];
//                1.组成了连接mysql语句连接数据库$dsn信息
//                2.通过传过来的配置信息$config中提取相对应的['db_password']信息
                $password = $config['db_password'];
//                1.运行连接数据库语句
//                2.传进去对应信息连接数据库
                $pdo = new PDO($dsn,$user,$password);
//                1.设置异常错误
//                2.通过设置异常错误，会把下面mysql中出现的错误信息输出，便于查找
                $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//                1.设置字符集
//                2.通过从数据库配置项提取$config['db_charset']设置数据库字符集信息
                $pdo->query("SET NAMES " . $config['db_charset']);
//                1.把连接信息存到静态属性中
//                2.再次登录就可以直接查看此静态信息，如果有数据就免于再次连接连接数据库
                self::$pdo = $pdo;

//                1.跟try一起使用的函数
//                2.如果try中运行时数据库有错误，catch会把错误接收到输出错误
            }catch (PDOException $e){

//                1.输出错误代码
//                2.通过ectch输出错误，方便在页面查找错误原因修改
                exit($e->getMessage());

            }

    }

//【执行有结果集sql语句 P方法】

//       1.构建P函数
//       2.通过P函数实现执行sql语句，来从数据库中提取信息的过程
    public function  q($sql){
//            1.连接数据库
//            2.通过catch{},可以接收到在try{}中连接mysql时出现的错误信息输出出来，便于查找万一出现问题
            try{
//                  1.写入数据库
//                  2.$spl语句是写入数据库的动作，需要用$pdo->query方法写入数据库（query方法用于执行有结果集的数据库操作）
                $result=self::$pdo->query($sql);
//                  1.获得数据库$data
//                  2.得到的数据，取出赋值给$data，得到数据库，方便页面显示内容从中取出
                $data=$result->fetchAll(PDO::FETCH_ASSOC);
//                  1.返回$data数据信息
//                  2.返回信息通过调用q函数就能实现提取出数据库信息
                return $data;
//                1.跟try一起使用的函数
//                2.如果try中运行时数据库有错误，catch会把错误接收到输出错误
            }catch (PDOException $e){
//                1.输出错误代码
//                2.通过ectch输出错误，方便在页面查找错误原因修改
                exit($e->getMessage());
            }
    }


//【执行无结果集sql语句 e方法】

//       1.构建P函数
//       2.通过P函数实现执行sql语句，来从数据库中提取信息的过程
    public function e($sql){
//            1.连接数据库
//            2.通过catch{},可以接收到在try{}中连接mysql时出现的错误信息输出出来，便于查找万一出现问题
            try{
//                  1.写入数据库
//                  2.$spl语句是写入数据库的动作，需要用$pdo->exec方法写入数据库（exec方法用于执⾏无结果
                return self::$pdo->exec($sql);
//            1.跟try一起使用的函数
//            2.如果try中运行时数据库有错误，catch会把错误接收到输出错误
            }catch (PDOException $e){
//                1.输出错误代码
//                2.通过ectch输出错误，方便在页面查找错误原因修改
                exit($e->getMessage());
            }
    }


//【获得主键的方法】
//    1.创建getPri获得主键的方法
//    2.我们要通过调用这个方法获得所选信息在表中的主键名， 以便于编辑，查询时查找信息
    public function getPri(){
//        1.查询表结构，并获取出来
//        2.我们根据查看表名获取表结构，从表结构中可以查看出哪个是主键
        $desc=$this->q("DESC {$this->table}");
//        通过打印输出，查看表结构信息时数组形式
//      p($desc);
//        1.构建$priField变量
//        2.用来存储查找到的主键名
        $priField='';
        foreach ($desc as $v){
//            1.判断如果查询到了哪个键值中的key对应的键值为'PRI'
//            2.则说明此数组中就是主键
            if($v['Key'] == 'PRI'){
//                1.获取表格主键名赋值到变量$priField
//                2.因为表格不一样，主键也是不确定的，用一个变量来赋值可以方便调用
                $priField=$v['Field'];
//                p($priField);
//                1.结束当前运行
//                2.查到主键信息了结束当前代码循环运行
                break;
            }
        }
//        1.返出当前变量值
//        2.返出当前变量，就是返出当前主键名。方便调用次方法时直接使用
        return  $priField;
    }



//【sql语句条件信息 where方法】

//       1.构建where方法
//       2.通过where方法实现组合连接sql语句实现条件添加
    public function where($where){
//          1.调用私有属性$where，赋值 " WHERE(条件提示) $where(条件语句)“
//          2.组合完整的where条件语句，注意"WHERE"前面有个空格，防止组合语句的时候忘了加空格跟前面语句连起来无法识别
        $this->where = " WHERE {$where}";
//        1.返回当前方法
//        2.通过返回当亲where方法实现调用where直接输出条件sql语句
        return $this;
    }



//【获取信息 get方法】


//  获得数据库全部信息
//      1.构建get方法
//      2.通过调用get方法，获得数据库中信息
    public function get()
    {
//        1.构建查看表格信息的sql语句
//        2.通过执行p函数执行这条sql语句，实现数据库信息的提取
        $sql = "SELECT * FROM {$this->table} {$this->where}";
//        1.调用q函数，传参进去sql语句
//        2.通过引用P函数把sql传进去，执行语句获得数据库信息，返回给本身，通过调用get函数就能实现提取数据库表格信息
        return $this->q($sql);
    }

//  查找特定数据库信息

//    1.构建查找数据库信息的find方法
//    2.通过调用find方法实现调用数据库信息,$pri就是查找的主键值，例如find(4),就是articl表的 主键名(aid)= 4
    public function  find($pri){
//        1.获得主键字段，比如cid还是aid
//        2.如果是Article::find(4),那么现在$priField它是aid，通过调用getPri()方法获得主键，存在$priField中，方便调用
        $priField= $this->getPri();
//        1.组合获得where查找条件
//        2.通过调用where方法获得查询的mql语句中中“WHERE 条件”，那么$this->where的值是 WHERE aid=4
        $this->where("{$priField} = {$pri}");
//        p($this->where("{$priField} = {$pri}"));
//        1.组合sql语句
//        2.组合一条查看表信息的sql语句，条件就是组合好的where条件
        $sql="SELECT * FROM {$this->table} {$this->where}";
//        p($sql);
//        1.调用q方法，执行sql查询语句
//        2.通过有结果集的q方法执行sql语句获得要查询的数据，赋值给$data
        $data=$this->q($sql);
//        查看获得数据为二维数组
//      p($data);
//        1.把获得二维数组变成一位数组
//        2.变成一维数组方便从中提取信息
        $data=current($data);
//        查看获得数组为一维数组
//      p($data);
//        1.把获得的$data数据赋值给存储数据的属性$data
//        2.把获得的信息赋值给数据库的data
        $this->data=$data;
//        1.返回当前的数据
//        2.返回当前的数据，通过逐层返回在页面显示出信息
        return $this;
    }

    public function findArray($pri){
        $obj = $this->find($pri);
        return $obj->data;
    }


    public function toArray(){
        return $this->data;
    }
//    1.构建统计中数据的方法
//    2.通过调用此方法获得所需统计数据的总量
    public function count($field='*'){
//        1.组合sql语句
//        2.通过调用$this->table,当前的表名，和$this->where当前的条件
        $sql="SELECT count({$field}) as FROM {$this->table} {$this->where()} ";
//        1.运行sql语句
//        2.通过q方法运行sql语句，获得信息
        $data=$this->q($sql);

        return $data[0]['c'];

    }


}