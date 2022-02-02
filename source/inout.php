<?php
#Written by KramWell.com - 19/SEPT/2018
#Fully operational crypto wallet for telegram with the ability to tip users, play games, win money and lots more!

#-this script will check for transactions going in and out of the wallet and log to the database.


require_once 'jsonRPCClient.php';
$bitcoin = new jsonRPCClient('http://username:password@localhost:8080/');

//connect to db
$conn = new mysqli("localhost","username","password","tg_wallet");
// check connection
if ($conn->connect_error) {
	logFile('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
	trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
}

generateWalletAddress();

checkAllTxBalance();

  
######################################################
#	generate wallet for user
######################################################
function generateWalletAddress(){ #/wallet

	#select all users who want an address
	$result = mysqli_query($GLOBALS['conn'],"SELECT userid FROM users WHERE address = 'PROCESS'");
	$row_cnt = mysqli_num_rows($result);

	#check if any users want an address
	if ($row_cnt > 0){
		echo "Found (". $row_cnt .") users to generate addresses for.";
		
		while($row = mysqli_fetch_array($result))
		{
			$userid = $row['userid'];
			
			#generate address
			$address = @$GLOBALS['bitcoin']->getnewaddress("$userid");
	
			if ($address){
				#insert into db
				$sql = "UPDATE users SET address='$address' WHERE userid='$userid'";
				if (!($GLOBALS['conn']->query($sql))) {
					echo "cant insert address for user $userid";
					logFile("cant insert address for user $userid");
				}else{
					#RETURN TRUE;
					echo " : ".$row['userid'] . " > " . $address;
					logFile("Created user: ".$row['userid'] . " With address: " . $address);
				}	
			}else{
				echo "Error getting address for user " . $row['userid'];
				logFile("Error getting address for user " . $row['userid']);
			}
			#here is the user id to 
			
		}//end sql loop	
	}
	mysqli_free_result($result);	
}  
  
######################################################  
#	get all the tx balances from wallet and 
#	only add the new ones to the db
######################################################
function checkAllTxBalance(){ #/balance

	# gets all transactions
	$userTransactions = $GLOBALS['bitcoin']->listtransactions();
	
	if ($userTransactions){
	
		foreach ($userTransactions as $transaction) {
			
			#try and insert every transaction into db and compare, update amounts etc.
			
			$account = $transaction['account'];
			$address = $transaction['address'];
			$category = $transaction['category'];
			$amount = $transaction['amount'];
			$txid = $transaction['txid'];
			$timereceived = $transaction['timereceived'];
			
			$confirmations = $transaction['confirmations'];
			
			if ($account){
				if ($category = 'receive'){
					$sql = "INSERT INTO txelicoin (account, address, category, amount, txid, timereceived) VALUES ('$account', '$address', '$category', '$amount', '$txid', '$timereceived')";
					if ($GLOBALS['conn']->query($sql)) {
						echo "inserted record $account : $amount : $address</br>";
						logFile("inserted record $account : $amount : $address");
						
						#now it has inserted record we must do something with the data we have to update balance of the account, and wallet address.
						
						$query = "SELECT balance FROM users WHERE userid='$account' AND address='$address'";
						$result = mysqli_query($GLOBALS['conn'], $query);
						$row = mysqli_fetch_assoc($result);
						mysqli_free_result($result);
						if ($row == true){
							#$row['balance'];
							#we have play balance of the user and wallet submitted to, now we have to update the balance
							
							$newbalance = $row['balance'] + $amount;
							
							$sql = "UPDATE users SET balance='$newbalance' WHERE userid='$account' AND address='$address'";
							if (!($GLOBALS['conn']->query($sql))) {
								echo "cant update amount for account $account";
								logFile("cant update amount for account $account");
							}else{
								#RETURN TRUE;
								echo "updated amount from". $row['balance'] ."to $newbalance</br>";
								logFile("updated amount from". $row['balance'] ."to $newbalance for user $account");
							}						
		
						} #end if found					
					} #end insert tx
				} #end receive tx only
			} #end account
			
		} 
		
	}else{
		echo "error listing transactions";
		logFile("error listing transactions");
		
	}

}

##############################################################
# place to dump any info from variable
##############################################################
function logFile($output){
	$timestamp = date("Y-m-d h:i:s");
	$myFile = "log.txt";
	$fh = fopen($myFile, 'a') or die("can't open file");
	fwrite($fh,"# $timestamp # $output\n");
	fclose($fh);
}

###########################  
 
#listtransactions
#print_r($bitcoin->listtransactions('600870606')); echo "\n";

  
  #echo "Help: ".$bitcoin->help()."\n";
  
  #generate new address
  #print_r($bitcoin->getnewaddress('donations')); echo "\n";
  
  #get address by account label
  #print_r($bitcoin->getaccountaddress('donations')); echo "\n";

  #get balance of account label
  #print_r($bitcoin->getbalance('600870606')); echo "\n";
  
  #checkAllTxBalance();
 
 
  
?>