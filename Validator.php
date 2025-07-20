<?php

// validate.php

include_once "DB.php";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class Validator {

    public static function validate(array $data, array $rules ): array {

        // The Validator is also created with scalability in mind. 
        // If we want to add a new rule, we simply add a new case.

        $pdo = NULL; // We will use PDO only if the $rules requiring it.
        $errors = [];
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';
            foreach ($fieldRules as $rule => $param) {
                if( is_int($rule) ) { $rule = $param; $param = null; }
                switch ($rule) {
                    case 'required':
                        if( empty($value) ) { $errors[$field][] = ucfirst($field).' is required.'; }
                        break;
                    case 'email':
                        if( !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL) ) { $errors[$field][] = ucfirst($field).' must be a valid email address.'; }
                        break;
                    case 'numeric':
                        if( !empty($value) && !is_numeric($value) ) { $errors[$field][] = ucfirst($field).' must be a numeric value.'; }
                        break;
                    case 'selection':
                        if( !empty($value) && (!is_array($param) || !in_array($value,array_keys($param))) ) $errors[$field][] = ucfirst($field).' must be on of this choices: '.implode(",",$param);
                        break;
                    case 'length':
                        if( !empty($value) && strlen($value) != $param ) { $errors[$field][] = ucfirst($field).' must be '.$param.' characters long. '.strlen($value).' given'; }
                        break;
                    case 'min_length':
                        if( !empty($value) && strlen($value) < $param ) { $errors[$field][] = ucfirst($field).' must be at least '.$param.' characters long.'; }
                        break;
                    case 'max_length':
                        if( !empty($value) && strlen($value) > $param ) { $errors[$field][] = ucfirst($field).' must not exceed '.$param.' characters.'; }
                        break;
                    case 'sanitized_text':
                        if( !empty($value) && !preg_match('/^[\p{L}\s]+$/u', $value) ) { 
                            $errors[$field][] = ucfirst($field).' must contain only letters and spaces.'; 
                        }
                        break;
                    case 'unique':
                        if( !$pdo ) { $pdo = DB::getConnection(); }
                        if( !empty($value) ) {
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$param} WHERE {$field} = :value"); $stmt->bindParam(':value', $value); $stmt->execute();
                            if( $stmt->fetchColumn() > 0 ) { $errors[$field][] = ucfirst($field).' already exists.'; }
                        }
                        break;
                }
            }
        }
        return $errors;
    }
}
