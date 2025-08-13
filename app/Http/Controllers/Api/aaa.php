<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class  extends Controller
{


calculation - get
https://api1.inperson.com.br/profiles/skills pega o retorno e retorna como skills
https://api1.inperson.com.br/profiles/adjectives pega o retorno e retorna como adjectives









    $_SESSION['vacancie'] = $vacanciee;
    $existingPerson = $personModel
    ->where('REPLACE(REPLACE(REPLACE(document_number, ".", ""), "-", ""), "/", "")', $cpf)
    ->where('id_company', $idCompany)
    ->first();

    if ($existingPerson) {
      $dataperson = [
          'company_reference'  => $postData['company_reference'] ?? '',
          'name'               => $postData['name'] ?? '',
          'document_number'                => $cpf ?? '',
          'personal_phone'              => $postData['phone'] ?? '',
          'adress_zip'                => $postData['cep'] ?? '',
          'adress_street'                => $postData['rua'] ?? '',
          'adress_complement'                => $postData['complemento'] ?? '',
          'linkedin'                => $postData['linkedin'] ?? '',
          'nascimento'                => $postData['birth'] ?? '',
          'adress_number'             => $postData['number'] ?? '',
          'id_company' => $idCompany,
          'complemento'        => $postData['complemento'] ?? '',
          'primary_color'      => $postData['primary_color'] ?? '',
          'personal_email'      => $postData['email'] ?? '',
          'id_person_type'      => 2,

      ];

      // Atualizar a pessoa existente
      $personModel->update($existingPerson['id_person'], $dataperson);
      $id_person = $existingPerson['id_person'];


      
  } else {       $dataperson = [
      'company_reference'  => $postData['company_reference'] ?? '',
      'name'               => $postData['name'] ?? '',
      'document_number'                => $cpf ?? '',
      'personal_phone'              => $postData['phone'] ?? '',
      'adress_zip'                => $postData['cep'] ?? '',
      'adress_street'                => $postData['rua'] ?? '',
      'adress_complement'                => $postData['complemento'] ?? '',
      'linkedin'                => $postData['linkedin'] ?? '',
      'nascimento'                => $postData['birth'] ?? '',
      'adress_number'             => $postData['number'] ?? '',
      'id_company' => $idCompany,
      'complemento'        => $postData['complemento'] ?? '',
      'primary_color'      => $postData['primary_color'] ?? '',
      'personal_email'      => $postData['email'] ?? '',
      'id_person_type'      => 2,
    


}
