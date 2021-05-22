$(document).ready(function () {
		
	/******* Sunil Code - Video Call *****/
			
			$("#btnMute").show();
			$("#btnUnMute").hide();
			$("#btnDisableVideo").show();
			$("#btnEnableVideo").hide();
			$("#lblDoctorMsg").hide();
			$("#lblDoctorMsg1").hide();
			$("#dvCallEnd").hide();
			
			var apiKey = $("#apiKey").val();
			// var sessionId = "2_MX40NjcyMjM0Mn5-MTU4ODc2NTM3NTU0M35na1BJTHlZUEpvcGIrazB3WG9wUjBMcGF-UH4";
			// var token = "T1==cGFydG5lcl9pZD00NjcyMjM0MiZzaWc9MzliMWI0YTQ3NGE1NTA4YjkxN2JhYTMxNTZlNDNjNGM3MjhiNTU4YTpzZXNzaW9uX2lkPTJfTVg0ME5qY3lNak0wTW41LU1UVTRPRGMyTlRNM05UVTBNMzVuYTFCSlRIbFpVRXB2Y0dJcmF6QjNXRzl3VWpCTWNHRi1VSDQmY3JlYXRlX3RpbWU9MTU4ODc2NTM5OCZub25jZT0wLjAwOTA4ODc5MjkzOTk2NTIyOSZyb2xlPXB1Ymxpc2hlciZleHBpcmVfdGltZT0xNTg5MzcwMTk2JmluaXRpYWxfbGF5b3V0X2NsYXNzX2xpc3Q9"
			
			var sessionId;
			var token;
						
			generateToken();
					
			function generateToken(){
				var request = {
					'doctor_id': $("#user_doctor_id").val(), 
					'patient_id': $("#user_patient_id").val(), 
					'appointment_id': $("#user_appointment_id").val()
				}
				$.ajax({
					type: 'POST',
					data: request,
					dataType: 'json',
					url: site_url + "patient/get_video_conf_token",
					success: function (response) {
						
						if(response.status == true)
						{
							console.log(response);
							isVideoCall = true;
							
							sessionId = response.session_id;
							token = response.token_id;
							
							initializeSession();
							send_pushwoosh_notification();
						}
					}
				});
			}

			function send_pushwoosh_notification(){
				var request = {
					'doctor_id': $("#user_doctor_id").val(), 
					'patient_id': $("#user_patient_id").val(), 
					'appointment_id': $("#user_appointment_id").val()
				}
				$.ajax({
					type: 'POST',
					data: request,
					dataType: 'json',
					url: site_url + "patient/send_pushwoosh_notification",
					success: function (response) {
						
					}
				});
			}

			function updateConnectionId(connection_id){
				var request = {
					'doctor_id': $("#user_doctor_id").val(), 
					'patient_id': $("#user_patient_id").val(), 
					'appointment_id': $("#user_appointment_id").val(),
					'connection_id' : connection_id
				}
				$.ajax({
					type: 'POST',
					data: request,
					dataType: 'json',
					url: site_url + "patient/update_connection_id",
					success: function (response) {
						if(response.status == true) {
							
						}
					}
				});
			}
			
			// Handling all of our errors here by alerting them
			function handleError(error) {
			  if (error) {
				alert(error.message);
			  }
			}
			
			function initializeSession() {
	
			  var session = OT.initSession(apiKey, sessionId);
			  
			   session.on({
				    connectionCreated: function (event) {
					updateConnectionId(session.connection.connectionId);
					if(session.connections.length() == 1)
					{
						 console.log("Doctor is yet to start the teleconsultation. Please wait.");
						 $("#lblDoctorMsg").show();
						 $("#lblDoctorMsg1").hide();
					}
					else
					{
						$("#lblDoctorMsg").hide();
					   $("#lblDoctorMsg1").hide();
					}
				   },
				   connectionDestroyed: function connectionDestroyedHandler(event) {
					 console.log("Doctor disconnected");
					 session.disconnect();
				   }
				 });

			  // Subscribe to a newly created stream
			  session.on('streamCreated', function(event) {
				  session.subscribe(event.stream, 'subscriber', {
					insertMode: 'append',
					width: '100%',
					height: '100%',
					style: {buttonDisplayMode: 'off'}
				  }, handleError);
				});
			  	session.on("sessionDisconnected", function(event) {
				    // session.disconnect(); 
				    console.log("The session disconnected. " + event.reason);
				    $("#lblDoctorMsg").hide();
				    $("#buttonsWrap").hide();
					$("#lblDoctorMsg1").show();
				});
				session.on("signal", function(event) {
					console.log("Signal data: " + event.data);
				});

			  // Create a publisher
			  var publisher = OT.initPublisher('publisher', {
				insertMode: 'append',
				width: '100%',
				height: '100%',
				style: {buttonDisplayMode: 'off'},
				publishAudio:true, 
				publishVideo:true
			  }, handleError);

			  // Connect to the session
			  session.connect(token, function(error) {
				// If the connection is successful, publish to the session
				if (error) {
				  handleError(error);
				} else {
				  session.publish(publisher, handleError);
				}
			  });
			  
			  $("#btnMute").click(function(){
				  $("#btnMute").hide();
     			  $("#btnUnMute").show();
				  publisher.publishAudio(false); 
			  });
			  $("#btnUnMute").click(function(){
				  $("#btnMute").show();
     			  $("#btnUnMute").hide();
				  publisher.publishAudio(true); 
			  });
			  
			  $("#btnDisableVideo").click(function(){
				  $("#btnDisableVideo").hide();
				  $("#btnEnableVideo").show();
				  publisher.publishVideo(false); 
			  });
				$("#btnEnableVideo").click(function(){
				  $("#btnDisableVideo").show();
				  $("#btnEnableVideo").hide();
				  publisher.publishVideo(true); 
			  });
			  
			   $("#btnEndCall").click(function(){
				   session.disconnect(); 
				  $("#dvCallEnd").show();
				  $("#dvVideo").hide();
				   	// var request = {
						// 'doctor_id': $("#user_doctor_id").val(), 
						// 'patient_id': $("#user_patient_id").val(), 
						// 'appointment_id': $("#user_appointment_id").val()
					// }
					// $.ajax({
						// type: 'POST',
						// data: request,
						// dataType: 'json',
						// url: site_url + "patient/end_video_conf_call",
						// success: function (data) {
    					  // console.log(data);
						  // session.disconnect(); 
						  // $("#dvCallEnd").show();
						  // $("#dvVideo").hide();
						// }
					// });
			  });
			}
			
			/******* End Code ******/
});