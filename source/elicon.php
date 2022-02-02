<?php
#Written by KramWell.com - 19/SEPT/2018
#Fully operational crypto wallet for telegram with the ability to tip users, play games, win money and lots more!

$botToken = "000000000:XXXXXXXXXXXXXXXXXXXXXXX"; #elicoin
$url = "https://api.telegram.org/bot".$botToken;

$update = file_get_contents("php://input");
$update = json_decode($update, TRUE);

$addonSend = "?disable_notification=TRUE&parse_mode=HTML&disable_web_page_preview=TRUE&";

##############################################################

$isCPUtalkBotId = $update['callback_query']['message']['from']['id'];

#LOGGING OF SENT TO TELEGRAM
checkJSON("log.txt", $update);

if ($isCPUtalkBotId == "000000000"){ #this is my bot replying.
	$replyOrSend = "/editMessageText";
	$snip = $update['callback_query']['data'];
	$callback_id = $update['callback_query']['id'];	
	$message = $update['callback_query']['data'];
	$chat_id = $update['callback_query']['message']['chat']['id'];
	$message_id = $update['callback_query']['message']['message_id'];
	$from_id = $update['callback_query']['message']['from']['id'];
	$username = $update['callback_query']['from']['username'];	
}else{
	$replyOrSend = "/sendMessage";
	$from_id = $update['message']['from']['id'];
	$chat_id = $update['message']['chat']['id'];
	$snip = $update['message']['text'];
	$message = $update['message']['text'];
	$message_id = $update['message']['message_id'];
	$new_member_id = $update['message']['new_chat_member']['id'];
	$isBot = $update['message']['from']['is_bot'];
	$username = $update['message']['from']['username'];
}

if (!is_numeric($chat_id)){
	displayError($url, $chat_id, $message_id, "Error: chat id not valid!");
}
if (!is_numeric($from_id)){
	displayError($url, $chat_id, $message_id, "Error: from id not valid!");
}
if (!is_numeric($message_id)){
	displayError($url, $chat_id, $message_id, "Error: message id not valid!");
}

#$isPrivate = 0;
#if ($from_id == $chat_id){
#	$isPrivate = 1;
#}

$new_member_welcome='';
if ($new_member_id){
	
	$new_member_username = $update['message']['new_chat_member']['username'];
	if (!$new_member_username){
		#user has no handle
		$new_member_welcome = " ".$update['message']['new_chat_member']['first_name'];
	}else{
		$new_member_welcome = " @". $new_member_username;	
		#add this to DB, but first check if bot	
	}	

$snip = '/start';
}

##############################################################	
# RPS- Rock Paper Scissors
##############################################################	

#to do this we need to initiate a game by interacting with the bot-

#user says /rps amount
#bot says choose a thing- r - p - s
#

#find the number 1-20.

#when user guesses, it will check the db for the number. then it will compare and update.
/*

if ($snip == '/guess@ElicoinBot' || $snip == '/guess'){


	//connect to db
	$conn = new mysqli("localhost","root","","tg_wallet");
	// check connection
	if ($conn->connect_error) {
	  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
	}
	
	$query = "SELECT * FROM guessno WHERE userid='000000000' AND id='1'";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	if ($row == true){	
		
	#take away 1 from users balance and add to guesspot Balance?
	#check if number is correct or not
	#change bot number.
		
		#$reply = $row['userid'];
		
		#here we need to check the number against the users number.
		if ($arr[1] == $row['guess']){

		#log traction here.
		$timestamp = time();
		$sql = "INSERT INTO transfers (response, messageid, userid, amountsent, timestamp, username, recepuser, senderbalb, senderbala, recepbalb, recepbala) VALUES ('$response', '$message_id', '$from_id', '$arr[2]', '$timestamp', '$username', '$recepUser', '$senderbalb', '$balanceAfter', '$recepbalb', '$newbalance')";
		$conn->query($sql);
		
		}else{
			
		}
		
	}


		
		$reply = "
		
YOUR GUESS: <b>4</b>
NUMBER: <b>12</b>

Sorry not this time.	
		
		";
  
		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id"
		);
		
}


*/


##############################################################	
# DISPLAY FIGHT
##############################################################	
if (mb_substr($snip, 0, 6, 'utf-8') == '/fight'){

if ($from_id == $chat_id){
	displayError($url, $chat_id, $message_id, "This function can only be used publicly");
}

	if (preg_match("/[^A-Za-z0-9\_]/", $username)){
		displayError($url, $chat_id, $message_id, "Error: your @handle is not valid! $username");
	}

	if (!$username){
	displayError($url, $chat_id, $message_id, "You must have an @handle to fight!");
	}	

	if ($isCPUtalkBotId == "000000000"){ #this is my bot replying.
		$from_id = $update['callback_query']['from']['id'];	
	}
	
	if ($snip == '/fight' || $snip == '/fight@ElicoinBot'){
		displayError($url, $chat_id, $message_id, "To fight someone, both parties must have an @handle present.\n\nUsage: /fight (amount)");
	}
	
	$arr = explode(" ",$snip);
	
	#$arr[1] - this is the amount 100
	
	#check if amount is number
	if (preg_match("/[^0-9]/", $arr[1])){
		displayError($url, $chat_id, $message_id, "Error: Amount specified to fight with is not a whole number!");	
	}	
	
	//connect to db
	$conn = new mysqli("localhost","root","","tg_wallet");
	// check connection
	if ($conn->connect_error) {
	  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
	}

		$query = "SELECT balance FROM users WHERE username='$username'";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($result);
		if ($row == false){
			displayError($url, $chat_id, $message_id, "You have no funds available, please use /deposit address to add.");	
		}
		#check if have enough balance to fight
		$senderbalb = $row['balance'];
		
		mysqli_free_result($result);
	
		$query = "SELECT messageid FROM fight WHERE userid='$from_id' AND amount='$arr[1]'";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($result);
		$message_id_to_remove = $row['messageid'];
		if ($row == false){

			#displayError($url, $chat_id, $message_id, "fresh user $arr[1]");
		
			#fresh user, check if amount is found in db. if so then fight- if not then add.
			
			$balanceAfter = $senderbalb - $arr[1];
			$recepuser = 'FIGHT';
			if ($balanceAfter < 0){
				displayError($url, $chat_id, $message_id, "Not enough funds, please use /deposit address to add.");
			}

			$sql = "UPDATE users SET balance='$balanceAfter' WHERE username='$username'";
			if ($conn->query($sql) === FALSE) {
				displayError($url, $chat_id, $message_id, "Error updating balance, please try again.");
			}
			
			#balance has now been updated in users wallet.
			
			/*$reply = executeQuery($postfields, $url."/getChatMembersCount", TRUE);		

			*/	

			$sql = "SELECT amount,username,userid FROM fight WHERE amount='$arr[1]' ORDER BY uid LIMIT 1"; # AND userid NOT LIKE '$from_id' 
			$result = mysqli_query($conn, $sql);
			
			$row = mysqli_fetch_assoc($result);
			
			#displayError($url, $chat_id, $message_id, "test".$row['amount']);
			
			if (mysqli_num_rows($result) > 0) {
				// output data of each row
				#$row = mysqli_fetch_assoc($result);

				$recepuser = $row['username'];

				// sql to delete a record
				$sql = "DELETE FROM fight WHERE userid='$row[userid]' AND amount='$arr[1]'";
				if ($conn->query($sql) === FALSE) {
					$response = 12;
				}		
				
				$response = 4;	
				
				$reply = "TEST";
				
				#if ($isCPUtalkBotId <> "000000000"){ #this is not my bot replying.
					#delete message as button is clicked
					#we need to get the message id of the button?

					$postfields = array(
						'chat_id' => "$chat_id",
						'message_id' => "$message_id_to_remove"
					);
					$reply1 = deleteMessage($postfields, $url."/deleteMessage", TRUE);	
					#displayError($url, $chat_id, $message_id, "$reply1");
				#}
				
				#here we have two people who want to fight based on oldest for the same amount playing for, 
				#we have also confirmed that they are not the same person.
				#now we have to send some funky alerts
				#

				#

				$fightOrder = mt_rand(1,2);
				
				if ($fightOrder == 1){
					$player1 = $recepuser;
					$player2 = $username;
				}else{
					$player1 = $username;		
					$player2 = $recepuser;					
				}

				
				#get winner- 
				$winner = @file_get_contents('https://www.random.org/integers/?num=1&min=1&max=2&col=1&base=10&format=plain&rnd=new');
				if (!$winner){
					$winner = mt_rand(1,2);
				}
				
				if ($winner == 1){
					$winnerIs = $player1;
				}else{
					$winnerIs = $player2;
				}
				



		

				$amountWon = $arr[1] + $arr[1];
				$arr[1] = $amountWon;
				
				if ($recepuser == $winnerIs){
					#if recepuser is the winner then- get recep user balance
					
					
					$query = "SELECT balance FROM users WHERE username='$recepuser'";
					$result = mysqli_query($conn, $query);
					$row = mysqli_fetch_assoc($result);
					if ($row == false){
						$response = 10;		
					}else{
						$senderbalb = $row['balance'];
						$balanceAfter = $senderbalb + $amountWon;			
					}

					$usernameTemp = $username;
					$username = $recepuser;
					$recepuser = $usernameTemp;					
					
				}else{
					$balanceAfter = $senderbalb + $amountWon;
				}


					$sql = "UPDATE users SET balance='$balanceAfter' WHERE username='$winnerIs'";
					if ($conn->query($sql) === FALSE) {
						$response = 11;	
					}

					
	
				
				#send a message to user saying that 	
				$reply = "@$player1 and @$player2 are face to face in the ring.. the crowd are cheering!! it's a one punch knockout, the bell rings! but who will be the winner?";
				$reply_return = executeQuery(array('chat_id' => "$chat_id",'text' => "$reply",'message_id' => "$message_id"), $url.$replyOrSend.$addonSend, TRUE);
				Sleep(mt_rand(5,7));
				
					$reply_return = json_decode($reply_return, true);
					$new_message_id = $reply_return['result']['message_id'];


					
				switch (mt_rand(0,1)) {
					case 0:
				$reply = "@$player1 gave @$player2 a fanatic left hook and they hit the floor hard! but @$player2 managed to somehow stumble to their feet and catch @$player1 by surprise...";				
						break;
					case 1:
				$reply = "@$player1 punched @$player2 as hard as they could! but @$player2 was quicker and managed to get a swing in at just the right time...";

						break;
					case 2:
						echo "i equals 2";
						#break;
				}
				
				$reply_return = executeQuery(array('chat_id' => "$chat_id",'text' => "$reply",'message_id' => "$new_message_id"), $url."/editMessageText".$addonSend, TRUE);

					$reply_return = json_decode($reply_return, true);
					$new_message_id = $reply_return['result']['message_id'];
				
				Sleep(mt_rand(5,7));
				$reply_return = executeQuery(array('chat_id' => "$chat_id",'text' => "The judges have decided...\n\nThe crowd is silent...\n\nAnd the winner is....",'message_id' => "$new_message_id"), $url."/editMessageText".$addonSend, TRUE);
				
					$reply_return = json_decode($reply_return, true);
					$message_id = $reply_return['result']['message_id'];				
				
				Sleep(mt_rand(4,5));
					$replyOrSend = "/editMessageText";
					
				$reply = "!! Congratulations!! @$winnerIs !! \xF0\x9F\x8E\x89 You have won (<b>$arr[1]</b>) Elicoin! Go get yourself cleaned up kid! we'll be needing you for the next round.";	
					
					
			}else{
				#insert intodb.
				
				#here we insert fight into db
				$timestamp = time();
				$sql = "INSERT INTO fight (userid, amount, timestamp, username, messageid) VALUES ('$from_id', '$arr[1]', '$timestamp', '$username', '$message_id')";
				if ($conn->query($sql) === TRUE) {
					#echo "inserted record";
					$response = 6;
					$reply = "@$username wants to fight for (<b>".$arr[1]."</b>) Elicoin!";	

					$keyboard = array(
					"inline_keyboard" => array(
					array(
						array(
							"text" => "## \xF0\x9F\x91\x8A !FIGHT! \xF0\x9F\x92\xA5 ##",
							"callback_data" => "/fight $arr[1]"
						)
					),				
					)); 		
									
					$postfields = array(
						'chat_id' => "$chat_id",
						'text' => "$reply",
						'message_id' => "$message_id",
						'reply_markup' => json_encode($keyboard)
					);
					
					#here we need to post and get the data back- then store the messageid in DB
					$findMessageID = executeQuery($postfields, $url."/sendMessage".$addonSend, TRUE); 
					$findMessageID = json_decode($findMessageID, true);
					$new_message_id = $findMessageID['result']['message_id'];
					
					if ($new_message_id){
						
						#update fight message id in db to returned one.
						$sql = "UPDATE fight SET messageid='$new_message_id' WHERE username='$username' AND messageid='$message_id'";
						$conn->query($sql);
						
					}
					
				}else{
					$reply = "Error inserting fight?";
					$response = 5;

				}			
				
				
			}
			
			
		}else{
			
			#now we remove the fight from DB
			
			
			
			// sql to delete a record
			$sql = "DELETE FROM fight WHERE userid='$from_id' AND amount='$arr[1]'";

			if ($conn->query($sql) === TRUE) {
				#record deleted
				
				#give user back the balance owed.
				$balanceAfter = $senderbalb + $arr[1];
				
				$sql = "UPDATE users SET balance='$balanceAfter' WHERE username='$username'";
				if ($conn->query($sql) === FALSE) {
					displayError($url, $chat_id, $message_id, "Error updating balance, please try again.");
				}
				
				$recepuser = 'FIGHT-REFUND';
				$response = 7;
				
				if ($isCPUtalkBotId <> "000000000"){ #this is not my bot replying.
					#delete message as button is clicked
					#we need to get the message id of the button?

					$postfields = array(
						'chat_id' => "$chat_id",
						'message_id' => "$message_id_to_remove"
					);
					$reply1 = deleteMessage($postfields, $url."/deleteMessage", TRUE);	
					#displayError($url, $chat_id, $message_id, "$reply1");
				}					
				
				#user is the same we must delete message (somehow) and give back amount to account?
				$reply = "Fight canceled, @$username you have been refunded (".$arr[1].") Elicoin";			
				#userid and amount is same we must cancel
					
			}else{
				#record not deleted
				displayError($url, $chat_id, $message_id, "Error could not edit fight data.");
			}
			
			#$senderbalb
			#$arr[1]
			
		}

	#log traction here.
	$timestamp = time();
	$sql = "INSERT INTO transfers (response, messageid, userid, amountsent, timestamp, username, recepuser, senderbalb, senderbala, recepbalb, recepbala) VALUES ('$response', '$message_id', '$from_id', '$arr[1]', '$timestamp', '$username', '$recepuser', '$senderbalb', '$balanceAfter', '0', '0')";
	$conn->query($sql);

	mysqli_free_result($result);

	$postfields = array(
	'chat_id' => "$chat_id",
	'text' => "$reply",
	'message_id' => "$message_id"
	);		
	
	#exit here or execute
	if ($response == 6){
		exit;
	}

}

##############################################################	
# DEV FUND
##############################################################	
if ($snip == '/fund@ElicoinBot' || $snip == '/fund'){

#check balance here
	//connect to db
	$conn = new mysqli("localhost","root","","tg_wallet");
	// check connection
	if ($conn->connect_error) {
	  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
	}		

#here we need to insert the withdraw request into db

#take away from balance first, then insert for processing.

	$query = "SELECT balance FROM users WHERE username='ElicoinBot'";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	if ($row == false){
		displayError($url, $chat_id, $message_id, "Error getting dev funds.");	
	}
		
		$reply = "Telegram Community Fund: 
		
(<b>".$row['balance']."</b>) ELICOIN
		
To add to the funds please send your tips to @ElicoinBot";
  
		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id"
		);
		
}

##############################################################	
# DISPLAY VERSION
##############################################################	
if ($snip == '/version@ElicoinBot' || $snip == '/version'){
		
		$reply = "Telegram Tip Bot for elicoin
-created by @KramWell

# Version <b>1.0.0</b> #
-Initial Release

# Version <b>1.0.1</b> #
-Fixed money tip bug: found by @L1337l1337
-Cleaned up code

# Version <b>1.0.2</b> #
-Fixed [at]handle tip bug: found by @janstim

# Version <b>1.0.3</b> #
-Fixed bug where new users who never interacted with the bot couldn't see private command.

Any bugs, suggestions please let me know!";
  
		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id"
		);
		
}
##############################################################	
# DISPLAY WHY ELICOIN MESSAGE
##############################################################	
if ($snip == '/why@ElicoinBot' || $snip == '/why'){
		
		$reply = "<b>Why Elicoin?</b>

  -  Thanks to YescryptR16 CPU only hashing algorithm, everyone can mine Elicoin on their own computer with just CPU. Mining on GPU is significantly slower than on CPU.
  
  -  Transactions within Elicoin blockchain are 10 times faster than Bitcoin.
  
  -  Elicoin blockchain can handle 20 times more transactions than Bitcoin within the same time.
  
  -  Difficulty on Elicoin is retargeted after every mined block (aprox. every minute), compared to Bitcoin that retargets difficulty every 2016 blocks (approx. every 14 days).
  ";
		$keyboard = array(
		"inline_keyboard" => array(
		array(
			array(
				"text" => "<< back",
				"callback_data" => "/start"
			)
		),				
		)); 		
		
		
		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id",
			'reply_markup' => json_encode($keyboard)
		);
		
	}
##############################################################	
# DISPLAY WHY Coin specifications MESSAGE
##############################################################	
if ($snip == '/coin@ElicoinBot' || $snip == '/coin'){		
		
		$reply = "<b>Coin Specifications:</b>
  	 
<b>Coin name</b>: 	Elicoin (ELI)
<b>Total number of coins</b>: 	10 000 000 ELI
<b>Initial block reward</b>: 	10 ELI
<b>Block halving</b>: 	every 500 000 blocks
<b>Block time</b>: 	1 minute
<b>Block size</b>: 	4 MB (1 MB base size + 3 MB for SegWit)
<b>Transactions per block</b>: 	8400
<b>Algorithm</b>: 	YescryptR16
<b>Difficulty retarget</b>: 	Every block (DarkGravityWave ver. 3)
<b>Premine</b>: 	NONE";
		$keyboard = array(
		"inline_keyboard" => array(
		array(
			array(
				"text" => "<< back",
				"callback_data" => "/start"
			)
		),				
		)); 		
		
		
		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id",
			'reply_markup' => json_encode($keyboard)
		);
		
	}
##############################################################	
# DISPLAY START MESSAGE
##############################################################	
if ($snip == '/start@ElicoinBot' || $snip == '/start'){
		
		$reply = "<b>Welcome!</b>$new_member_welcome I am the Elicoin bot

Feel free to ask anything Elicoin related :)
	
See the links below to find useful information about this amazing cryptocurrency!
	
To tip someone, both parties must have an @handle present. Then simply use /tip @handle amount.

/price : see current price.
/tip : tip someone
/coin : coin specifications.
/why : why elicoin?
/help : help about elicoin and tip function.

";	
		
		
		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id"
		);
		
	}

##############################################################	
# DISPLAY PRICE
##############################################################
if ($snip == '/price@ElicoinBot' || $snip == '/price'){
	$update = file_get_contents('https://coinlib.io/api/v1/coin?key=6a3bf75d30cc9c19&pref=USD&symbol=ELI2');
	$json_array = json_decode($update, TRUE);

	#btc value 
	#print_r($json_array['markets']['0']['price']);

	#usd value
	#print_r($json_array['price']);
	#$json_array['price'] total_volume_24h low_24h high_24h 
		
	$reply = "Price: $" .round($json_array['price'], 5). "
BTC: " .$json_array['markets']['0']['price']. "
24hr High: $" .round($json_array['high_24h'], 5) . "
24hr Low: $" .round($json_array['low_24h'], 5) . "
24hr Volume: $" .round($json_array['total_volume_24h'], 5);	

	
	$keyboard = array(
	'inline_keyboard' => array(
		array(
			array(
				'text' => 'coinlib.io',
				'url' => 'https://coinlib.io/coin/ELI2/Elicoin'
			)
		)
	)); 	
	
		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id",
			'reply_markup' => json_encode($keyboard)
		);
		
}

##############################################################	
# DISPLAY BALANCE
##############################################################
if ($snip == '/balance@ElicoinBot' || $snip == '/balance'){

if ($from_id <> $chat_id){
	displayError($url, $chat_id, $message_id, "This function can only be used privately, see @ElicoinBot");
}

if (!$username){
displayError($url, $chat_id, $message_id, "You must have an @handle see your balance!");
}

if (preg_match("/[^A-Za-z0-9\_]/", $username)){
	displayError($url, $chat_id, $message_id, "Error: your @handle is not valid!");
}

		//connect to db
		$conn = new mysqli("localhost","root","","tg_wallet");
		// check connection
		if ($conn->connect_error) {
		  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
		}
	
		$query = "SELECT balance FROM users WHERE username='$username'";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($result);
		if ($row == true){
			#echo "user balance $userBalance";
			$reply = "\xF0\x9F\x92\xB5 - Your balance is [" .$row['balance'] ."] Elicoin";		
		}else{
			$reply = "You have no wallet address associated. Please click /deposit to get started!";		
		}
		
		mysqli_free_result($result);	
	
/*	
		$keyboard = array(
		"inline_keyboard" => array(
		array(
			array(
				'text' => 'Refresh',
				'callback_data' => '/balance'
			),
			array(
				'text' => 'Deposit',
				'callback_data' => '/deposit'
			),
			array(
				'text' => 'Withdraw',
				'callback_data' => '/withdraw'
			)			
		)					
		)); 		
*/		
		
		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id"
		);		
}	

##############################################################	
# DISPLAY DEPOSIT
##############################################################
if ($snip == '/deposit@ElicoinBot' || $snip == '/deposit'){

if ($from_id <> $chat_id){
	displayError($url, $chat_id, $message_id, "This function can only be used privately, see @ElicoinBot");
}

if (!$username){
displayError($url, $chat_id, $message_id, "You must have an @handle to deposit!");
}

if (preg_match("/[^A-Za-z0-9\_]/", $username)){
	displayError($url, $chat_id, $message_id, "Error: your @handle is not valid!");
}

	#here we generate a wallet address by putting in userID to DB and waiting for other side to pick it up.

	//connect to db
	$conn = new mysqli("localhost","root","","tg_wallet");
	// check connection
	if ($conn->connect_error) {
	  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
	}
	
	$query = "SELECT address FROM users WHERE username='$username'";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	if ($row == true){
		
		$reply = "Your deposit address is: ". $row['address'];
		
		#if address is 0 then it is awaiting address key to be generated.
		if ($row['address'] == "PROCESS"){
			$reply = "Your account is being prepared, please wait a maximum of 2 minutes for this to complete.";
		}elseif ($row['address'] == "TIPPED"){
			$sql = "UPDATE users SET userid='$from_id',address='PROCESS' WHERE username='$username'";
			if ($conn->query($sql) === TRUE) {
				$reply = "We are now preparing your account, please allow a maximum of 2 minutes for this to complete. Check for address generation again by using /deposit";
			}else{				
				$reply = "Error adding user, Please try again.";					
			}
		}
	}else{
		$timestamp = time();
		$sql = "INSERT INTO users (userid, address, timestamp, username) VALUES ('$from_id', 'PROCESS', '$timestamp', '$username')";
		if ($conn->query($sql) === TRUE) {
			#echo "inserted record";
			$reply = "We are now preparing your account, please allow a maximum of 2 minutes for this to complete. Check for address generation again by using /deposit";
		}else{
			$reply = "Error adding user, Please try again.";				
		}
	}
		
		mysqli_free_result($result);
		
		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id"
		);
		
}	
##############################################################	
# DISPLAY TEST
##############################################################
/*
if ($snip == '/test@ElicoinBot' || $snip == '/test'){

	#$reply = "To tip someone, both parties must have an @handle present.\n\nUsage: /tip (@handle) (amount) [optional: reason for tipping]";	
	
		$postfields = array(
			'chat_id' => "$chat_id"
		);

$reply = executeQuery($postfields, $url."/getChatMembersCount", TRUE);		

		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id"
		);

}
*/
##############################################################	
# DISPLAY WITHDRAW
##############################################################

if (mb_substr($snip, 0, 9, 'utf-8') == '/withdraw'){

if ($from_id <> $chat_id){
	displayError($url, $chat_id, $message_id, "This function can only be used privately, see @ElicoinBot");
}

	if (!$username){
	displayError($url, $chat_id, $message_id, "You must have an @handle to withdraw!");
	}

	if (preg_match("/[^A-Za-z0-9\_]/", $username)){
		displayError($url, $chat_id, $message_id, "Error: your @handle is not valid!");
	}

	$reply = '';
		
	if ($snip == '/withdraw' || $snip == '/withdraw@ElicoinBot'){
		displayError($url, $chat_id, $message_id, "To withdraw, please send: /withdraw [receiving address] (amount)");
	}


	$arr = explode(" ",$snip);
	
	#$arr[1] - this is the rec address
	#$arr[2] - this is the amount 100

	if (strlen($arr[1]) <> 34){
		displayError($url, $chat_id, $message_id, "Receiving address is not the required length.");
	} 	

	#check if amount is number
	if (preg_match("/[^0-9]/", $arr[2]))
	{
		displayError($url, $chat_id, $message_id, "Amount is not a number.");	
	}

	//connect to db
	$conn = new mysqli("localhost","root","","tg_wallet");
	// check connection
	if ($conn->connect_error) {
	  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
	}		

#here we need to insert the withdraw request into db

#take away from balance first, then insert for processing.

	$query = "SELECT balance,address FROM users WHERE username='$username'";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	if ($row == false){
		displayError($url, $chat_id, $message_id, "You have no funds available, please use /deposit address to add.");	
	}

	
	if ($row['address'] == $arr[1]){
		displayError($url, $chat_id, $message_id, "You can't send funds to yourself.");
	}
	
	#check if have enough balance to send
	$senderbalb = $row['balance'];
	$balanceAfter = $senderbalb - $arr[2];
	if ($balanceAfter < 0){
		displayError($url, $chat_id, $message_id, "Not enough funds, please use /deposit address to add.");
	}

	$sql = "UPDATE users SET balance='$balanceAfter' WHERE username='$username'";
	if ($conn->query($sql) === TRUE) {
		
		#log transaction here.
		$timestamp = time();
		$sql = "INSERT INTO transfers (response, messageid, userid, amountsent, timestamp, username, recepuser, senderbalb, senderbala, recepbalb, recepbala) VALUES ('9', '$message_id', '$from_id', '$arr[2]', '$timestamp', '$username', '$arr[1]', '$senderbalb', '$balanceAfter', '0', '0')";
		if ($conn->query($sql) === TRUE) {
			#echo "inserted record";			
			$reply = "We are now preparing your withdraw request, please allow a maximum of 2 minutes plus block confirmation time for this to complete.";
		}else{
			$reply = "Error preparing withdraw request!";
		}			
		
	}else{
		$reply = "Error updating user balance!";
	}	


	$postfields = array(
		'chat_id' => "$chat_id",
		'text' => "$reply",
		'message_id' => "$message_id"
	);
	
}
##############################################################	
# DISPLAY TIP
##############################################################

if (mb_substr($snip, 0, 4, 'utf-8') == '/tip'){

	if (preg_match("/[^A-Za-z0-9\_]/", $username)){
		displayError($url, $chat_id, $message_id, "Error: your @handle is not valid! $username");
	}

	if ($snip == '/tip' || $snip == '/tip@ElicoinBot'){
		displayError($url, $chat_id, $message_id, "To tip someone, both parties must have an @handle present.\n\nUsage: /tip (@handle) (amount) [optional: reason for tipping]");
	}

	$reply = '';
	
		if (!$username){
		displayError($url, $chat_id, $message_id, "You must have an @handle to tip!");
		}
	
		$arr = explode(" ",$snip);
		
		#$arr[1] - this is the @handle
		#$arr[2] - this is the amount 100
		#$arr[3+] - this is the text
		
		if (mb_substr($arr[1], 0, 1, 'utf-8') !== '@'){ #if it is @
			displayError($url, $chat_id, $message_id, "Error: @handle specified is not valid!");
		}

		#check here for same user tipping is the same as the user
		if (strtolower($username) == strtolower(ltrim($arr[1], '@'))){
			displayError($url, $chat_id, $message_id, "You can't tip yourself!");
		}
		
		if (preg_match("/[^A-Za-z0-9\_]/", ltrim($arr[1], '@'))){ #if it just contains numbers and letters
			displayError($url, $chat_id, $message_id, "Error: the @handle specified is not valid!");
		}	
		
		#check if amount is number
		if (preg_match("/[^0-9]/", $arr[2])){
			displayError($url, $chat_id, $message_id, "Error: Amount specified to send is not a whole number!");	
		}

		if ($arr[3]){
		
			$snip = str_replace($arr[0] . " ","",$snip);
			$snip = str_replace($arr[1] . " ","",$snip);
			$snip = str_replace($arr[2] . " ","",$snip);
				
			$reply = " Reason: " . $snip . " ";
			
		}
	
		//connect to db
		$conn = new mysqli("localhost","root","","tg_wallet");
		// check connection
		if ($conn->connect_error) {
		  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
		}		

		$query = "SELECT balance FROM users WHERE username='$username'";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($result);
		if ($row == false){
			displayError($url, $chat_id, $message_id, "You have no funds available, please use /deposit address to add.");	
		}

		#check if have enough balance to send
		$senderbalb = $row['balance'];
		$balanceAfter = $senderbalb - $arr[2];
		if ($balanceAfter < 0){
			displayError($url, $chat_id, $message_id, "Not enough funds, please use /deposit address to add.");
		}

		$sql = "UPDATE users SET balance='$balanceAfter' WHERE username='$username'";
		if ($conn->query($sql) === TRUE) {						
		
			#first we see if user sender exists in db, if so check if has balance, if so pay recipient	
			
			#pay recipient by checking if in db, if so update balance, if not create shadow user, unconfirmed
			$recepUser = str_replace("@","",$arr[1]);
			$query = "SELECT balance FROM users WHERE username='$recepUser'";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_assoc($result);
			if ($row == true){
				
				$recepbalb = $row['balance'];
				$newbalance = $recepbalb + $arr[2];

				$sql = "UPDATE users SET balance='$newbalance' WHERE username='$recepUser'";
				if ($conn->query($sql) === TRUE) {
					#echo "updated record";
					$reply = "@" . $username . " tipped [". $arr[2] . "] Elicoin to " .$arr[1] . $reply;
					$response = 0;
				}else{
					$reply = "Error tipping user, Please try again.";
					$response = 3;
				}		
				
			}else{
				
				#this is here for logging,
				$newbalance = $arr[2];
				
				$timestamp = time();
				$sql = "INSERT INTO users (userid, address, timestamp, username, balance) VALUES ('0', 'TIPPED', '$timestamp', '$recepUser', $newbalance)";
				if ($conn->query($sql) === TRUE) {
					#echo "inserted record";
					$reply = "@" . $username . " tipped [". $arr[2] . "] Elicoin to " .$arr[1] . $reply;
					$response = 0;
				}else{								
					$reply = "Error tipping user, Please try again.";
					$response = 2;
				}						

			}
					
		}else{
			$reply = "Error tipping user, Please try again.";
			$response = 1;
		}	

		#log traction here.
		$timestamp = time();
		$sql = "INSERT INTO transfers (response, messageid, userid, amountsent, timestamp, username, recepuser, senderbalb, senderbala, recepbalb, recepbala) VALUES ('$response', '$message_id', '$from_id', '$arr[2]', '$timestamp', '$username', '$recepUser', '$senderbalb', '$balanceAfter', '$recepbalb', '$newbalance')";
		$conn->query($sql);
	
		mysqli_free_result($result);
	
		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id"
		);
	
}
##############################################################	
# DISPLAY DISCLAIMER
##############################################################
if ($snip == '/disclaimer@ElicoinBot' || $snip == '/disclaimer'){

	$reply = "This bot has been created to help tip or transfer a small number of coins through Telegram for enjoyment purposes and not as an official tool of monetary purposes. Although there is no limit on the number of coins you can send or receive throughout the tip-bot, we recommend you only keep a limited balance at any given time. Only you have access to your tip-bot balance. Creators are not responsible for any loss or theft of coins.

1.No minimum tip amount.
2.No fees will be charged for tipping.
3.There are no withdrawal fees.
4.We are not liable for wrong transactions and wrong tip amount.";	
	
		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id"
		);
		
}	

##############################################################	
# DISPLAY HELP
##############################################################
if ($snip == '/help@ElicoinBot' || $snip == '/help'){

	$reply = "# Commands:
/tip - This command let you tip someone using their @handle
/balance - See your Elicoin tip balance
/deposit - Show deposit address
/withdraw - Withdraw your Elicoin tips
/disclaimer - Show disclaimer

/price : See current price.

/fund : Telegram Community Fund

This bot was made for the community by @KramWell.
If you'd like to contribute (ideas, translations, bugs, etc.) please contact me. 
NOTE: You can click these commands instead of typing them.";	

		$keyboard = array(
		"inline_keyboard" => array(
		array(
			array(
				'text' => 'Wallets',
				'url' => 'https://github.com/elicoin/elicoin#wallets'
			),
			array(
				'text' => 'Github',
				'url' => 'https://github.com/elicoin/elicoin'
			)			
		),
		array(
			array(
				'text' => 'Exchange',
				'url' => 'https://crex24.com/exchange/ELI-BTC'
			),
			array(
				'text' => 'Donations',
				'url' => 'https://github.com/elicoin/elicoin#donations'
			)
		),
		array(
			array(
				'text' => 'Bitcointalk',
				'url' => 'https://bitcointalk.org/index.php?topic=3028302'
			),
			array(
				'text' => 'Discord',
				'url' => 'https://discord.gg/cv77fUp'
			)			
		)						
		)); 		
		
		
		$postfields = array(
			'chat_id' => "$chat_id",
			'text' => "$reply",
			'message_id' => "$message_id",
			'reply_markup' => json_encode($keyboard)
		);
		
}	
	
##############################################################
#REPLACE OR DISPLAY
##############################################################
executeQuery($postfields, $url.$replyOrSend.$addonSend);	
	
##############################################################
#EXECUTE QUERY - SEND TO CURL
##############################################################
function executeQuery($postfields, $urlToSend, $returnBack = FALSE){

	if (!$curld = curl_init()) {
	exit;
	}

	curl_setopt($curld, CURLOPT_POST, true);
	curl_setopt($curld, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($curld, CURLOPT_URL,$urlToSend);
	curl_setopt($curld, CURLOPT_RETURNTRANSFER, true); #seemed to speed things up?
	
	$outputFromTelegram = curl_exec($curld);

	curl_close ($curld);
	
	checkJSON("output.txt", json_decode($outputFromTelegram, true));
	
	if ($returnBack == TRUE){
		RETURN $outputFromTelegram;
	}else{
		exit;
	}
}
##############################################################
#DELETE MESSAGE
##############################################################
function deleteMessage($postfields, $urlToSend, $returnBack = FALSE){

	if (!$curld = curl_init()) {
	exit;
	}

	curl_setopt($curld, CURLOPT_POST, true);
	curl_setopt($curld, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($curld, CURLOPT_URL,$urlToSend);
	#curl_setopt($curld, CURLOPT_RETURNTRANSFER, true); #seemed to speed things up?

	$output = curl_exec($curld);

	if ($returnBack == TRUE){
	RETURN $output;
	}
	curl_close ($curld);
	
}
##############################################################
# FOR SENDING ERROR OUTPUT TO USER
##############################################################
function displayError($url, $chat_id, $message_id, $reply = 'ERROR'){
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply"
	);
executeQuery($postfields, $url."/sendMessage".$addonSend);	
}
##############################################################
#output all results if dumpResult() is called.
##############################################################
function checkJSON($myFile, $update){

	$updateArray = print_r($update,TRUE);
	$fh = fopen($myFile, 'a') or die("can't open file");
	fwrite($fh, $updateArray."\n\n");
	fclose($fh);
}
	



?>