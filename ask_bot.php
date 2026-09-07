<?php
// Step 1: Define the hospital assistant context
$doctorContext = <<<EOD
You are a hospital assistant for Global Health Hospital in Dhaka. Here is the list of doctors and their specializations:

- Dr. Ayesha Rahman — Dermatology
- Dr. Tanvir Hossain — Neurology
- Dr. Nabila Karim — Pediatrics
- Dr. Imran Chowdhury — Orthopedics
- Dr. Ashraful — Cardiology
- Dr. Shafiqul Islam — Dentistry
- Dr. Farzana Akter — General Medicine
- Dr. Mahmud Hasan — Gastroenterology
- Dr. Laila Siddiqua — Endocrinology
- Dr. Rezaul Karim — ENT
- Dr. Nusrat Jahan — Gynecology
- Dr. Kamal Uddin — Pulmonology
- Dr. Shirin Alam — Psychiatry

When a patient describes symptoms, suggest the most appropriate doctor from this list.
EOD;

// Step 2: Get the user's message from the frontend
$userPrompt = $_POST['prompt'] ?? 'Hello';

// Step 3: Combine the context with the user's message
$fullPrompt = $doctorContext . "\n\nPatient: " . $userPrompt;

// Step 4: Prepare the request payload
$data = [
  'model' => 'llama3.2:1b',
  'prompt' => $fullPrompt,
  'stream' => false
];

// Step 5: Send POST request to Ollama's local API
$ch = curl_init('http://localhost:11434/api/generate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

// Step 6: Decode and return the model's response
$result = json_decode($response, true);
echo $result['response'] ?? 'No response from model.';
?>
