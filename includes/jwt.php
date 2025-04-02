<?php

/**
 * Creates a JWT (JSON Web Token).
 *
 * This function generates a JWT by encoding the header and payload, signing them with a private key,
 * and combining the three parts into a complete token.
 *
 * @param array $payload The payload data to include in the JWT.
 * @return string The generated JWT token.
 */
function createJWT(array|string $payload): string {
  // Step 1: Create the header (Header)
  $header = [
      'alg' => 'RS256', // RSA algorithm with SHA-256
      'typ' => 'JWT'    // Type of token (JWT)
  ];
  // Encode the header as a JSON string and then to Base64 URL-safe
  $headerEncoded = base64UrlEncode(json_encode($header));

  // Step 2: Create the payload (Payload)
  // Encode the payload as a JSON string and then to Base64 URL-safe
  $payloadEncoded = base64UrlEncode(json_encode($payload));

  // Step 3: Create the signature
  // Combine the encoded header and payload to form the data to sign
  $dataToSign = $headerEncoded . '.' . $payloadEncoded;
  // Sign the data using the private key with SHA-256 algorithm
  openssl_sign($dataToSign, $signature, PRIVATE_KEY, OPENSSL_ALGO_SHA256);
  // Encode the signature to Base64 URL-safe
  $signatureEncoded = base64UrlEncode($signature);

  // Step 4: Combine all parts (header, payload, signature) to form the JWT
  return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
}

/**
 * Verifies the JWT signature.
 *
 * This function checks the authenticity of a JWT by verifying its signature
 * against the data encoded in the header and payload, using the provided public key.
 * It uses OpenSSL to perform the signature verification.
 *
 * @param string $jwt The JWT token to verify.
 * @return bool True if the signature is valid, False otherwise.
 */
function verifyJWT(string $jwt): bool {
  // Split the JWT into its three parts: header, payload, and signature
  $parts = explode('.', $jwt);

  // If the JWT does not have exactly 3 parts, it is malformed
  if (count($parts) !== 3) {
      return false; // Invalid format
  }

  // Assign the parts to variables
  [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

  // Recreate the data to verify (header + payload)
  $dataToVerify = $headerEncoded . '.' . $payloadEncoded;

  // Decode the signature from Base64 URL-safe format
  $signature = base64UrlDecode($signatureEncoded);

  // Verify the signature using the public key and SHA256 algorithm
  return openssl_verify($dataToVerify, $signature, PUBLIC_KEY, OPENSSL_ALGO_SHA256) === 1;
}

/**
 * Encodes data to Base64 URL-safe format.
 *
 * The Base64 URL-safe encoding uses different characters from standard Base64:
 *   - '+' is replaced with '-'
 *   - '/' is replaced with '_'
 * Additionally, any trailing '=' padding is removed to ensure the URL-safe string does not contain special characters.
 *
 * @param string $data The data to encode.
 * @return string The Base64 URL-safe encoded data.
 */
function base64UrlEncode(string $data): string {
  // Encode the data to Base64, then replace URL-safe characters and remove padding '='
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Decodes a Base64 URL-safe encoded string.
 *
 * The Base64 URL-safe encoding uses different characters for encoding:
 *   - '-' replaces '+'
 *   - '_' replaces '/'
 * This function converts these characters back to their standard Base64 equivalents 
 * before calling the PHP `base64_decode` function to decode the data.
 *
 * @param string $data The Base64 URL-safe encoded data to decode.
 * @return string The decoded data.
 */
function base64UrlDecode(string $data): string {
  // Replace URL-safe characters ('-' with '+' and '_' with '/') and decode the Base64 data
  return base64_decode(strtr($data, '-_', '+/'));
}

/**
 * Function to extract and decode the payload from a JWT token.
 *
 * This function first verifies the JWT token using the `verifyJWT` function. If the token is valid,
 * it extracts and decodes the payload (the second part of the token), which is returned as an associative array.
 * If the token is invalid, it returns null.
 *
 * @param string $jwt The JWT token containing the data.
 * @return string|null The decoded payload if the token is valid, or null if the token is invalid.
 */
function getPayload(string $jwt) {
  // Verify the token using the verifyJWT function
  if (!verifyJWT($jwt)) {
      // If the token is invalid, return null
      return null;
  }

  // Split the token into its three parts: header, payload, and signature
  $jwtParts = explode('.', $jwt);

  // Get the second part of the token (the payload)
  $encodedPayload = $jwtParts[1];

  // Decode the payload from Base64 URL-safe encoding
  $decodedPayload = base64_decode($encodedPayload);

  // Decode the payload from JSON format to an associative array
  $decodedPayload = json_decode($decodedPayload, true);

  // Return the decoded payload
  return $decodedPayload;
}