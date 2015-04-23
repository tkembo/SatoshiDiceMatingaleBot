<?php
    //start round
    $startAmt = 100; //Satoshis
    $numThrows = 1000; //Number of times to throw the dice
    $secret = "<GET_THIS_SECRET_FROM_YOUR_URL>";
    $payOutAddress = "<ENTER_YOUR_BITCOIN_ADDRESS_HERE>";
    $firstround = file_get_contents("https://session.satoshidice.com/userapi/startround.php?secret=".$secret);
   
    $json_a=json_decode($firstround,true);
    
    $firsturl = "https://session.satoshidice.com/userapi/placebet.php?secret=".$secret."&betInSatoshis=".$startAmt."&id=".$json_a['id']."&serverHash=".$json_a['hash']."&clientRoll=3245&belowRollToWin=32000";
    //die($firsturl);
    $string= file_get_contents($firsturl);
    $json_b= json_decode($string,true);
    $startBalance = $json_b['userBalanceInSatoshis'];
    $balanceCounter = 0;

    echo "Opening balance: <strong>".$startBalance."</strong>";

    $newAmt=$startAmt;
    if ($json_b['status'] == "success"){
        $balanceCounter = $balanceCounter + $json_b['userBalanceInSatoshis'];
        echo ($json_b['message']."<br>");
        for ($i=1; $i<$numThrows; $i++){
            $nextURL = "https://session.satoshidice.com/userapi/placebet.php?secret=".$secret."&betInSatoshis=".$newAmt."&id=".$json_b['nextRound']['id']."&serverHash=".$json_b['nextRound']['hash']."&clientRoll=3245&belowRollToWin=32000";
            $nextString = file_get_contents($nextURL);
            $json_b=json_decode($nextString, true);
            if ($json_b['userBalanceInSatoshis'] > ($startBalance*2)){
                $amtToSend = $json_b['userBalanceInSatoshis'] - $startBalance;
                $amtToSend = $amtToSend / 10000000;
                $withdrawURL = file_get_contents("http://session.satoshidice.com/userapi/withdraw/?secret=".$secret."&address=".$payOutAddress."&amount=".$amtToSend);
                // The following line sends you your profit after your earnings double
		$json_withdraw=json_decode($withdrawURL,true);
                echo "<br> >>>>>>>>>>>>>>>>>>>>>>>>>>";
               	echo "Send ".$json_withdraw['amountWithdrawn']." BTC to your Bitcoin Address";
                echo "<br> >>>>>>>>>>>>>>>>>>>>>>>>>>";
                // Uncomment the following line if you want the bot to stop after your earnings double
		//die("........Earnings doubled. Game over................");
            }
            echo ("Result: <strong>".$json_b['bet']['result']."</strong> Balance: ".$json_b['userBalanceInSatoshis']*100000000);
            echo ($json_b['message']."<br>");
            if($json_b['bet']['result']=="loss"){
                $newAmt = $newAmt*2;
            }else{
                $newAmt = $startAmt;
            }
        } 
    }else{
        echo ($json_b['message']);
    }

    
?>


