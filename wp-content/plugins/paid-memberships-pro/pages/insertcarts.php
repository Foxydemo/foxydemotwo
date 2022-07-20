<?php
session_start();
$PQuantity=$_POST['PQuantity'];
$PName=$_POST['PName'];
$PAmount=$_POST['PAmount'];
$PCode=$_POST['PCode'];
$PFrequency=$_POST['PFrequency'];
$PStartDate=$_POST['PStartDate'];
$PEndDate=$_POST['PEndDate'];




$result = array();
if($PCode){

    if(isset( $_SESSION['cart'][$PCode])){
        $pre=$_SESSION['cart'][$PCode]['PQuantity'];
        $_SESSION['cart'][$PCode]=array('PQuantity'=>$pre+$PQuantity,'PName'=>$PName,'PAmount'=>$PAmount,'PCode'=>$PCode,'PFrequency'=>$PFrequency,'PStartDate'=>$PStartDate,'PEndDate'=>$PEndDate);
   $quantity=0;
   $price=0;
   $totalPrice=0;

   foreach($_SESSION['cart'] as $key => $value) {
    $quantity+=(int)$value['PQuantity'];
    $price+=(int)$value['PAmount'];
    $totalPrice=$price*$quantity;
   }
        foreach($_SESSION['cart'] as $key => $value) {
        



            echo "
            <form  action= '' method='POST'>
            <div class='col-md-6'>
            <div class='card border border-primary' style='width: 18rem;'>
            <img class='card-img-top' src='' alt='Card image cap'>
            <div class='card-body'>
              <h5 class='card-title'>$value[PName]</h5>
              <p class='card-text'>amount &nbsp; &nbsp; &nbsp; &nbsp; $value[PAmount]</p>
              <p class='card-text'>Quantity &nbsp; &nbsp; &nbsp; &nbsp; <input type='text' class='w-25'value='$value[PQuantity]' /></p>
              <p class='card-text'>Frequency  &nbsp; &nbsp; &nbsp; &nbsp; $value[PFrequency]</p>
              <p class='card-text'>weigth  &nbsp; &nbsp; &nbsp; &nbsp; 1.0 LBS</p>
              <p class='card-text'>product code  &nbsp; &nbsp; &nbsp; &nbsp; $value[PCode]</p>
              <p class='card-text'>start date  &nbsp; &nbsp; &nbsp; &nbsp; $value[PStartDate]</p>
              <p class='card-text'>enddate  &nbsp; &nbsp; &nbsp; &nbsp; $value[PEndDate]</p>
              
              <input type='hidden' id='quantity' value='$quantity'/>
              <input type='hidden' id='totalPrice' value='$totalPrice'/>
              

    <input type='hidden' value='$value[PCode]' class='pdel'/>
    <input type='hidden' value='del'class='del'/>
              <input type='button' class='btn btn-primary delbtn' value='remove' onclick='delFunction()'/>
            </div>
          </div>
          </div>
          </forn>
            
            
            
            
            
            ";
            
            
          }
        }
    
    else{

         $_SESSION['cart'][$PCode]=array('PQuantity'=>$PQuantity,'PName'=>$PName,'PAmount'=>$PAmount,'PCode'=>$PCode,'PFrequency'=>$PFrequency,'PStartDate'=>$PStartDate,'PEndDate'=>$PEndDate);
         $quantity=0;
         $price=0;
         $totalPrice=0;
      
         foreach($_SESSION['cart'] as $key => $value) {
          $quantity+=(int)$value['PQuantity'];
          $price+=(int)$value['PAmount']*(int)$value['PQuantity'];
          $totalPrice=$price;
         }
         foreach($_SESSION['cart'] as $key => $value) {
            echo "
            <form  action= '' method='POST'>
            <div class='col-md-6'>
            <div class='card' style='width: 18rem;'>
            <img class='card-img-top' src='' alt='Card image cap'>
            <div class='card-body'>
              <h5 class='card-title'>$value[PName]</h5>
              <p class='card-text'>amount &nbsp; &nbsp; &nbsp; &nbsp; $value[PAmount]</p>
              <p class='card-text'>Quantity &nbsp; &nbsp; &nbsp; &nbsp; <input type='text' class='w-5'value='$value[PQuantity]' /></p>
              <p class='card-text'>Frequency  &nbsp; &nbsp; &nbsp; &nbsp; $value[PFrequency]</p>
              <p class='card-text'>weigth  &nbsp; &nbsp; &nbsp; &nbsp; 1.0 LBS</p>
              <p class='card-text'>start date  &nbsp; &nbsp; &nbsp; &nbsp; $value[PStartDate]</p>
              <p class='card-text'>enddate  &nbsp; &nbsp; &nbsp; &nbsp; $value[PEndDate]</p>
              <input type='hidden' id='quantity' value='$quantity' />
              <input type='hidden' id='totalPrice' value='$totalPrice'/>
    
              <input type='button' class='btn btn-primary delbtn' value='remove' onclick='delFunction()'/>
            </div>
          </div>
          </div>
            </form>
          
            
            
            
            
            ";
            
            
          }
        if(isset($_POST['PDel'])=='PDel'){
            $PCode=$_POST['PCode'];
            foreach($_SESSION['cart'] as $key => $value) {
                if($value['PCode']===$PDel){
                    unset($_SESSION['cart'],$key);
                    $_SESSION['cart']=array_value($_SESSION['cart'] ) ;
                    echo 'deleted';
    
                }
    
        }
    }

    };

};


