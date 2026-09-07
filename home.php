<?php session_start(); ?>

<!DOCTYPE html>
<html>

<head>

	<title>MediPulse - Clinic Management System</title>

	<meta charset="UTF-8">

	<meta
		name="viewport"
		content="width=device-width, initial-scale=1">

	<!-- <link
		href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
		rel="stylesheet"> -->

	<!-- <link
		href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
		rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/icons/font/bootstrap-icons.css" rel="stylesheet">

	<style>
		.hero {

			background:
				linear-gradient(rgba(0, 0, 0, 0.45),
					rgba(0, 0, 0, 0.45)),
				url('assets/images/medipulse-hero.jpg') center center / cover no-repeat;

			color: white;

			padding: 120px 0;

			min-height: 550px;

			display: flex;

			align-items: center;

			text-shadow: 1px 1px 4px #000;

		}


		.hero .container {

			background-color: rgba(0, 0, 0, 0.65);

			padding: 45px;

			border-radius: 15px;

			max-width: 900px;

		}


		.hero h1 {

			letter-spacing: 1px;

		}


		.hero .lead {

			font-size: 1.25rem;

		}


		.section-title {

			margin-top: 60px;

			margin-bottom: 30px;

		}


		.feature-card {

			border: none;

			border-radius: 12px;

			box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);

			height: 100%;

			transition: transform 0.2s ease;

		}


		.feature-card:hover {

			transform: translateY(-5px);

		}


		.feature-icon {

			font-size: 40px;

			margin-bottom: 15px;

		}


		.chatbot {

			width: 300px;

			z-index: 9999;

		}


		@media (max-width: 768px) {

			.hero {

				padding: 80px 0;

			}

			.hero .container {

				padding: 30px 20px;

			}

			.chatbot {

				width: calc(100% - 30px);

				margin: 15px !important;

			}

		}
	</style>

</head>


<body>


	<!-- =========================================================
     HERO SECTION
========================================================= -->

	<section class="hero text-center">

		<div class="container">

			<h1 class="display-3 fw-bold">
				Welcome to MediPulse
			</h1>

			<p class="lead mt-3">
				Smart Clinic Management. Better Healthcare.
			</p>

			<p class="mb-4">
				Manage appointments, patient records, prescriptions,
				billing, and intelligent health prediction in one platform.
			</p>


			<div
				class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-4">

				<a
					href="patient_login.php"
					class="btn btn-outline-light btn-lg">
					<i class="bi bi-person"></i>
					I'm a Patient
				</a>


				<a
					href="doctor_login.php"
					class="btn btn-outline-light btn-lg">
					<i class="bi bi-person-badge"></i>
					I'm a Doctor
				</a>


				<a
					href="admin_login.php"
					class="btn btn-outline-light btn-lg">
					<i class="bi bi-shield-lock"></i>
					Admin Login
				</a>

			</div>

		</div>

	</section>



	<!-- =========================================================
     ABOUT SECTION
========================================================= -->

	<section class="container text-center">

		<h2 class="section-title">
			About MediPulse
		</h2>

		<p class="lead">

			MediPulse is a modern clinic management system designed
			to simplify healthcare management and improve the
			interaction between patients, doctors, and administrators.

		</p>

		<p>

			The platform provides appointment scheduling, patient
			medical profiles, prescriptions, billing management,
			and intelligent heart disease prediction to support
			doctors during clinical decision-making.

		</p>

	</section>



	<!-- =========================================================
     FEATURES SECTION
========================================================= -->

	<section class="container">

		<h2 class="section-title text-center">
			MediPulse Features
		</h2>


		<div class="row g-4">


			<div class="col-md-4">

				<div class="card feature-card text-center p-4">

					<div class="feature-icon">
						<i class="bi bi-calendar-check"></i>
					</div>

					<h5>
						Appointment Management
					</h5>

					<p class="text-muted">

						Patients can book, reschedule, and cancel
						appointments with available doctors.

					</p>

				</div>

			</div>



			<div class="col-md-4">

				<div class="card feature-card text-center p-4">

					<div class="feature-icon">
						<i class="bi bi-file-medical"></i>
					</div>

					<h5>
						Patient Medical Records
					</h5>

					<p class="text-muted">

						Maintain patient profiles, medical history,
						symptoms, and clinical information securely.

					</p>

				</div>

			</div>



			<div class="col-md-4">

				<div class="card feature-card text-center p-4">

					<div class="feature-icon">
						<i class="bi bi-heart-pulse"></i>
					</div>

					<h5>
						Heart Disease Prediction
					</h5>

					<p class="text-muted">

						Doctors can use the machine-learning-based
						prediction module as a clinical decision-support tool.

					</p>

				</div>

			</div>



			<div class="col-md-4">

				<div class="card feature-card text-center p-4">

					<div class="feature-icon">
						<i class="bi bi-prescription2"></i>
					</div>

					<h5>
						Digital Prescriptions
					</h5>

					<p class="text-muted">

						Doctors can create structured prescriptions
						and provide patients with downloadable prescription PDFs.

					</p>

				</div>

			</div>



			<div class="col-md-4">

				<div class="card feature-card text-center p-4">

					<div class="feature-icon">
						<i class="bi bi-receipt"></i>
					</div>

					<h5>
						Billing Management
					</h5>

					<p class="text-muted">

						Manage patient bills, payment status,
						and digital receipts efficiently.

					</p>

				</div>

			</div>



			<div class="col-md-4">

				<div class="card feature-card text-center p-4">

					<div class="feature-icon">
						<i class="bi bi-robot"></i>
					</div>

					<h5>
						AI Health Assistant
					</h5>

					<p class="text-muted">

						An AI-powered assistant helps users navigate
						healthcare-related questions and clinic services.

					</p>

				</div>

			</div>

		</div>

	</section>



	<!-- =========================================================
     CHATBOT
========================================================= -->

	<div
		id="chatbot"
		class="position-fixed bottom-0 end-0 m-4 p-3
         bg-light border rounded shadow chatbot">

		<h5>
			🤖 Ask MediPulse Assistant
		</h5>

		<div
			id="chat-log"
			style="
      height: 150px;
      overflow-y: auto;
      font-size: 14px;
    "></div>


		<input
			type="text"
			id="chat-input"
			class="form-control mt-2"
			placeholder="Describe your problem...">

	</div>



	<!-- =========================================================
     CONTACT SECTION
========================================================= -->

	<?php

	if (isset($_SESSION['contact_success'])) {

		echo '
      <div class="alert alert-success text-center">
        Thank you! Your message has been received.
      </div>
    ';

		unset($_SESSION['contact_success']);
	}

	?>


	<section class="container mt-5">

		<h2 class="section-title text-center">
			Contact MediPulse
		</h2>


		<div class="row">


			<div class="col-md-6">

				<form
					action="contact_submit.php"
					method="POST">

					<div class="mb-3">

						<label>
							Name:
						</label>

						<input
							type="text"
							name="name"
							class="form-control"
							required>

					</div>


					<div class="mb-3">

						<label>
							Email:
						</label>

						<input
							type="email"
							name="email"
							class="form-control"
							required>

					</div>


					<div class="mb-3">

						<label>
							Message:
						</label>

						<textarea
							name="message"
							class="form-control"
							rows="4"
							required></textarea>

					</div>


					<button
						type="submit"
						class="btn btn-primary">
						Send Message
					</button>

				</form>

			</div>


			<div class="col-md-6">

				<iframe
					src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.902831862118!2d90.3602!3d23.7386!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b85b1e1b1b1b%3A0x7a1c1c1c1c1c1c1c!2sLife%20%26%20Care%20Hospital%2C%20495%2F1%20Jhauchar%2C%20West%20Hazaribagh%2C%20Dhaka%201211!5e0!3m2!1sen!2sbd!4v1680000000000"
					width="100%"
					height="300"
					style="border:0;"
					allowfullscreen=""
					loading="lazy"></iframe>

			</div>

		</div>

	</section>



	<!-- =========================================================
     FOOTER
========================================================= -->

	<footer class="text-center py-4 mt-5 bg-light">

		<p class="mb-0">

			&copy;
			<?= date('Y') ?>
			MediPulse.
			All rights reserved.

		</p>

	</footer>



	<!-- =========================================================
     JAVASCRIPT
========================================================= -->

	<script src="doctors.js"></script>


	<script>
		const input = document.getElementById('chat-input');

		const log = document.getElementById('chat-log');


		let lastSuggestedDoctor = null;

		let awaitingBookingConfirmation = false;


		const affirmativeResponses = [

			"yes",
			"y",
			"sure",
			"of course",
			"ofcourse",
			"yeah",
			"yep",
			"ok",
			"okay",
			"certainly",
			"absolutely"

		];


		input.addEventListener('keypress', function(e) {

			if (e.key === 'Enter') {

				const userText = input.value.trim();

				if (!userText) return;


				log.innerHTML +=
					`<div>
          <strong>You:</strong>
          ${userText}
        </div>`;


				// Booking confirmation

				if (awaitingBookingConfirmation) {

					const normalized =
						userText.toLowerCase();


					if (affirmativeResponses.includes(normalized)) {

						log.innerHTML +=
							`<div>
              <strong>Bot:</strong>
              Great! Redirecting you to the patient portal...
            </div>`;


						setTimeout(() => {

							window.location.href =
								'index.php';

						}, 1500);


					} else {

						log.innerHTML +=
							`<div>
              <strong>Bot:</strong>
              No problem! Let me know if you have another concern.
            </div>`;

					}


					awaitingBookingConfirmation = false;

					lastSuggestedDoctor = null;

					input.value = '';

					return;

				}


				// Send message to backend

				fetch('ask_bot.php', {

						method: 'POST',

						headers: {
							'Content-Type': 'application/x-www-form-urlencoded'
						},

						body: 'prompt=' +
							encodeURIComponent(userText)

					})

					.then(res => res.text())

					.then(botReply => {

						log.innerHTML +=
							`<div>
            <strong>Bot:</strong>
            ${botReply}
          </div>`;


						// Extract doctor name

						const match =
							botReply.match(/Dr\.\s[\w\s]+/);


						if (match) {

							lastSuggestedDoctor =
								match[0];


							log.innerHTML +=
								`<div>
              <strong>Bot:</strong>
              Would you like to book an appointment with
              ${lastSuggestedDoctor}? (yes/no)
            </div>`;


							awaitingBookingConfirmation =
								true;

						}


						log.scrollTop =
							log.scrollHeight;

					});


				input.value = '';

			}

		});
	</script>


</body>

</html>