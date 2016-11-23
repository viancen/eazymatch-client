<?php

class Eazymatch_Error extends Exception {}
class Eazymatch_HttpError extends Eazymatch_Error {}

/**
 * The parameters passed to the API call are invalid or not provided when required
 */
class Eazymatch_ValidationError extends Eazymatch_Error {}

/**
 * The provided API key is not a valid Eazymatch API key
 */
class Eazymatch_Invalid_Key extends Eazymatch_Error {}
