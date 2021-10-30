<?php
class Tools
{
    static function connect(
        $host="localhost:3307",
        $user="root",
        $pass="123456",
        $dbname="shop")
    {
        $cs='mysql:host='.$host.';dbname='.$dbname.';charset=utf8;';
        $options=array(
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES UTF8'
        );

        try
        {
            $pdo=new
            PDO($cs,$user,$pass,$options);
            return $pdo;
        }

        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }
    }

    static function register($name,$pass,$imagePath)
    {
        $name = trim($name);
        $pass = trim($pass);
        $imagePath = trim($imagePath);

        if ($name=="" || $pass=="" )
        {
            echo "<h3/><span style='color:red;'>Fill All Required Fields!</span><h3/>";
            return false;
        }

        if (strlen($name)<3 || strlen($name)>30 || strlen($pass)<3 || strlen($pass)>30)
        {
            echo "<h3/><span style='color:red;'>Values Length Must Be Between 3 And 30!</span><h3/>";
            return false;
        }

    Tools::connect();

    $customer=new Customer($name, $pass, $imagePath);
    $err=$customer->intoDb();
    if ($err) {
        if($err==1062)
            echo "<h3/><span style='color:red;'>This Login Is Already Taken!</span><h3/>";
        else
            echo "<h3/><span style='color:red;'>Error code:".$err."!</span><h3/>";
            return false;
        }
        return true;
    }


}

class Customer
{
    protected $id; //user id
    protected $login;
    protected $pass;
    protected $roleId;
    protected $discount; //customer's personal discount
    protected $total; //total ammount of purchases
    protected $imagePath; //path to the image

    function __construct($login, $pass, $imagePath, $id=0)
    {
        $this->login=$login;
        $this->pass =$pass;
        $this->imagePath =$imagePath;
        $this->id =$id;
        $this->total =0;
        $this->discount =0;
        $this->roleId =2;
    }

    function intoDb()
    {
        try
        {
            $pdo=Tools::connect();
            $ps=$pdo->prepare("INSERT INTO Customers(login,pass,roleId,discount,total,imagePath) VALUES (:login,:pass,:roleId,: discount,:total,:imagePath)");
            $ar=(array)$this;
            array_shift($ar);
            $ps->execute($ar);
        }
        catch(PDOException $e)
        {
            $err=$e->getMessage();
            if(substr($err,0,strrpos($err,":"))=='SQLSTATE[23000]:Integrity constraint violation')
                return 1062;
            else
                return $e->getMessage();
        }
    }

    static function fromDb($id)
    {
        $customer = null;
        try
        {
            $pdo = Tools::connect();
            $ps = $pdo->prepare(("SELECT * FROM Customers WHERE id=?)");
            $res = $ps ->execute(array($id));
            $row = $res->fetch();
            $customer = new Customer($row['login'],$row['pass'], $row['imagePath'],$row['id']);
            return $customer;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }
    }

}

class Item
{
    public $id, $itemName, $catid, $pricein, $pricesale, $info, $rate, $imagePath, $action;

    function __construct($itemname, $catid, $pricein, $pricesale, $info, $imagePath, $rate=0, $action=0, $id=0)
    {
        $this->id=$id;
        $this->itemName=$itemName;
        $this->catid=$catid;
        $this->pricein=$pricein;
        $this->pricesale=$pricesale;
        $this->info=$info;
        $this->rate=$rate;
        $this->imagePath=$imagePath;
        $this->action=$action;
    }

    function intoDb()
    {
        try
        {
            $pdo=Tools::connect();
            $ps=$pdo->prepare("INSERT INTO Items (itemName, catid, pricein, pricesale, info, rate, imagePath, action)
            VALUES (:itemName, :catid, :pricein, :pricesale, :info, :rate, :imagePath, :action)");
            $ar=(array)$this;
            array_shift($ar);
            $ps->execute($ar);
        }
        catch(PDOException $e)
        {
            return $e->getMessage();
        }
    }

    static function fromDb($id)
    {
        $customer=null;

        try
        {
            $pdo=Tools::connect();
            $ps=$pdo->prepare(("SELECT * FROM Items WHERE id=?)");
            $res=$ps->execute(array($id));
            $row=$res->fetch();
            $customer=new Item($row['itemName'], $row['catid'], $row['pricein'], $row['pricesale'], $row['info'], $row['imagePath'], $row['rate'], $row['action'],$row['id']);
            return $customer;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }
    }

    static function GetItems($catid=0)
    {
        $ps=null;
        $items=null;

        try
        {
            $pdo=Tools::connect();
            if($catid == 0)
            {
                $ps=$pdo->prepare('select * from items');
                $ps->execute();
            } else {
                $ps=$pdo->prepare('select * from items where categoryid=?');
                $ps->execute(array($catid));
            }
        while ($row=$ps->fetch())
        {
            $item=new Item($row['itemname'],
            $row['catid'],
            $row['pricein'],
            $row['pricesale'], $row['info'],
            $row['imagepath'], $row['rate'],
            $row['action'],$row['id']);
            $items[]=$item;
        }
            return $items;
        }

        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }
    }

    function Draw()
    {
    echo "<div class='col-sm-3 col-md-3 col-lg-3 container' style='height:350px;margin:2px;'>";
    //itemInfo.php contains detailed info about product
    echo "<div class='row' style='margin-top:2px; background-color:#ffd2aa;'>";
    echo "<a href='pages/itemInfo.php?name=".$this->id."'class='pull-left' style='margin-left:10px; target='_blank'>";
    echo $this->itemName;
    echo "</a>";
    echo "<span class='pull-right' style='margin-right:10px;'>";
    echo $this->rate."&nbsp;rate";
    echo "</span>";
    echo "</div>";
    echo "<div style='height:100px; margin-top:2px; class='row'>";
    echo "<img src='".$this->imagePath." height='100px' />";
    echo "<span class='pull-right' style='margin-left:10px;color:red;font-size:16pt;'>";
    echo "$&nbsp;".$this->pricesale;
    echo "</span>";
    echo "</div>";
    echo "<div class='row' style='margin-top:10px;'>";
    echo "<p class='text-left col-xs-12'style='background-color:lightblue;overflow:auto;height:60px;'>";
    echo $this->info;
    echo "</p>";
    echo "</div>";
    echo "<div class='row' style='margin-top:2px;'>";
    echo "</div>";
    echo "<div class='row' style='margin-top:2px;'>";
    //creating cookies for the cart
    //will be explained later
    $ruser='';
    if(!isset($_SESSION['reg']) || $_SESSION['reg'] =="")
    {
        $ruser="cart_".$this->id;
    }
    else
    {
        $ruser=$_SESSION['reg']."_".$this->id;
    }
    echo "<button class='btn btn-success col-xs-offset-1 col-xs-10' onclick=createCookie('".$ruser."','".$this->id."')>
            Add To My Cart
          </button>";
    echo "</div>";
    }

    function DrawForCart()
    {
        echo "<div class='row' style='margin:2px;'>";
        echo "<img src='".$this->imagePath."'width='70px' class='col-sm-1 col-md-1 col-lg-1'/>";
        echo "<span style='margin-right:10px;background-color:#ddeeaa;color:blue;font-size:16pt' class='col-sm-3 col-md-3 col-lg-3'>";
        echo $this->itemName;
        echo "</span>";
        echo "<span style='margin-left:10px;color:red;font-size:16pt;background-color:#ddeeaa;' class='col-sm-2 col-md-2 col-lg-2' >";
        echo "$&nbsp;".$this->pricesale;
        echo "</span>";
        $ruser='';
        if(!isset($_SESSION['reg']) || $_SESSION['reg'] =="")
        {
            $ruser="cart_".$this->id;
        }
        else
        {
            $ruser=$_SESSION['reg']."_".$this->id;
        }
        echo "<button class='btn btn-sm btn-danger' style='margin-left:10px;'onclick=eraseCoo kie('".$ruser."')>x</button>";
        echo "</div>";
    }

    function Sale()
    {
        try
        {
            $pdo=Tools::connect();
            $ruser='cart';
            if(isset($_SESSION['reg']) && $_SESSION['reg'] !="")
            {
                $ruser=$_SESSION['reg'];
            }
    //Increasing total field for Customer
            $sql = "UPDATE Customers SET total=total + ? WHERE login = ?";
            $ps = $pdo->prepare($sql);
            $ps->execute(array($this->pricesale,$ruser));
    //Inserting info about sold item into table Sales
            $ins = "insert into Sales (customername,itemname,pricein,pricesale,datesale) values(?,?,?,?,?)";
            $ps = $pdo->prepare($ins);
            $ps->execute(array($ruser,$this->itemname,
            $this->pricein,$this->pricesale, @date("Y/m/d H:i:s")));
    //deleting item from Items table
            $del = "DELETE FROM Items WHERE id = ?";
            $ps = $pdo->prepare($del);
            $ps->execute(array($this->id));
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }
    }


}
