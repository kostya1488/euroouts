
<?php






$name = $_POST['name'];
$phone = $_POST['phone'];
$date = $_POST['date'];

$to = 'mail@partworks.org.ua'; 
$subject = 'Резюме 	Соискателя';    
$body ="ФИО: $name\n Номер телефона: $phone\n Время для звонка: $datе\n ";
$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
$headers.="From: $email\nReply-To: mail@partworks.org.ua\r\n";
$headers.="Content-type: text/plain; charset=UTF-8\r\n";
$headers.="Content-transfer-encoding: quoted-printable";
    
    if (mail ($to, $subject, $body, $headers)) {
     $result = '<div class="alert alert-success">Спасибо! Ваше письмо отправлено</div>'; 
    } else {  
     $result='<div class="alert alert-danger">Что-то не так... Попробуйте позже</div>';
    }





?>

<html><head>
	<base href="http://partworks.org.ua">
	<link rel="stylesheet" type="text/css" href="/css/styles.css">
	<link rel="stylesheet" type="text/css" href="/css/">
	<link rel="stylesheet" type="text/css" href="/css/">
	<link href="http://fonts.googleapis.com/css?family=Cuprum&amp;subset=latin,cyrillic" type="text/css" rel="stylesheet">
	<script>
		bAdmin=false;  
	</script>
	<script type="text/javascript" src="/admin/java.js"></script>
	<script type="text/javascript" src="/paginations.js"></script> 
	<script defer="" type="text/javascript" src="/admin/user.js"></script>
	<script type="text/javascript" src="/jbcallme/js/jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="/jbcallme/css/jquery.jbcallme.css">
	<script type="text/javascript" src="jbcallme/js/jquery.jbcallme.js"></script>
	<script type="text/javascript">
	$(function() {
		$('.callback').jbcallme();
	});
	
	
	</script>
	<script>
		var wheight;
	
		function BodyScroll() {
			if(document.getElementById("shadow") && document.getElementById("top-heder")){ 
				bheight = Number(document.body.scrollTop);
					//console.log (bheight);
				if (bheight>=105) {
					document.getElementById("shadow").setAttribute("class", "bfixex");
					document.getElementById("top-heder").setAttribute("class", "fixex");
					document.getElementById("top-heder").style.position="fixed";
					
				} else {
					document.getElementById("top-heder").style.position="relative";
					document.getElementById("top-heder").setAttribute("class", "");
					document.getElementById("shadow").setAttribute("class", "");
				}
					
				if (bheight>wheight) {
					document.getElementById("topbutton").style.display="block";
				} else {
					document.getElementById("topbutton").style.display="none";
				}
			}
		}
		

	</script>
	

	
<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
	<meta name="copyright" content="Права принадлежат ">
	<meta http-equiv="content-language" content="ru">
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="keywords" content="">
	<meta name="description" content="">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 
	<title>Главная</title>
</head>
<body id="shadow" onscroll="BodyScroll()" onafterprint="onPrintStart()">
   
											
											
											
											
											
											
											
											
											
											
											
											
											
											
											
											
	
<div id="content">
    <div class="nav">
        <div class="one-col">     													
																					<ul>		<li><a href="/o-kompanii">О КОМПАНИИ</a></li>		<li><a href="/rabotodatelyam">работодателям</a></li>		<li><a href="/soiskatelyam">соискателям</a></li>						<li><a href="/kontakti">контакты</a></li>		<li class="empty"></li></ul>
																															 </div>
    </div><div class="header">
			<div class="">
				<div class="logo" onclick="window.location=''"></div>
				<p>подбор персонала <br> в Киеве</p>
				<div class="work-time">
					<div class="separator"></div>
					Часы работы:<br>
					<span>пн. - пт. <span class="marked">09:00 - 18:00</span><br>сб. и вс. - выходной</span>
				</div>

				<div class="phone-number">
					<div class="separator"></div>
					<p><span class="city-code">(098)</span>&nbsp;795-64-58</p>
					<p class="callback">Обратный звонок</p>
				</div>
				<div class="phone-number">
					<div class="separator"></div>
					<p><span class="city-code">(099)</span>&nbsp;957-10-29</p>
					<p class="callback">Обратный звонок</p>
				</div>
			</div>
		</div><div class="wrapper">   
											            
											
											            
													<link href="css/slider.css" type="text/css" rel="stylesheet">
		
		
		 
																															 
		
		
		</div>
		
		
		<div class="partner">
			<div class="one-col">
				<div class="header">
	<h1>Спасибо. Мы перезвоним ближайшым часом.</h1>
					
					
				</div>
				
			</div>
		</div>
																																																																																																																																																																 
																															    
											
											          
																																									</div>
    <div class="footer">
        <div class="one-col">
            <div class="info">
                Кадровый центр «PartWorks»<br>
                Адрес: Киев, ул. Бульвар дружбы, 10 А<br>
                Тел. 380(98) 795-64-58, 380(99) 957-10-29<br>
                E-Mail: mail@partworks.org.ua
            </div>
            <div class="link">
                <ul>
                    <div>&nbsp;   				
					<ul>							<li>
								<a href="/o-kompanii">
									О КОМПАНИИ								</a>
															</li>
													
													<li>
								
															</li>
													<li>
								<a href="/rabotodatelyam/ostavit-zayavku">
									ОСТАВИТЬ ЗАЯВКУ								</a>
															</li>
													
						</ul><ul>							<li>
								<a href="/soiskatelyam/vakansii"></a>
															</li>
													<li>
								<a href="/soiskatelyam/ostavit-rezyume">
									ОСТАВИТЬ РЕЗЮМЕ								</a>
															</li>
													<li>
								
															</li>
													<li>
								
															</li>
													<li>
								<a href="kontakti">
									КОНТАКТЫ								</a>
															</li>
						</ul>				
		 
																																									
                    </div>
                    <div class="no-margin"><br></div>
                </ul>
            </div>
            
            
        </div>
    </div>


																																																																																																																																																																																					<!--<a id=topbutton href="#" onclick='window.scrollTo(0,0);return false' title="Наверх" class="topbutton">Наверх</a>-->
	

	


</body></html>