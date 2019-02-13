<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Goutte\Client;
use Symfony\Component\CssSelector\CssSelectorConverter;

$app->post('/auth', function(Request $request, Response $response, array $args) use ($app) {
    $input = $request->getParsedBody();
    $username = $input['txt_username'];
    $password = $input['txt_password'];
    $client = new Client(['cookies' => true]);
    $crawler = $client->request('GET', 'http://mymancosa.com/login.php');
    $form = $crawler->selectButton('Login')->form();
    $crawler = $client->submit($form, array('txt_username' => $username, 'txt_password' => $password, 'category' => 'Student'));
    $link = $crawler->selectLink('My Info')->link();
    $crawler = $client->click($link);
    
    $studentInfo = $crawler->filter('#table tr')->each(function ($node, $i) {
        if($i>=1){
            $key1 = $null; 
            $value1 = $null;
            try{ $key1 = $node->filter("td:nth-child(1)")->text(); } catch (Exception $e) {}
            try{ $value1 = $node->filter("td:nth-child(3)")->text(); } catch (Exception $e) {}
            $_val1 = $null;
            if($key1 && $value1){
                $_val1 = [$key1, $value1];
            }

            $key2 = $null; 
            $value2 = $null;
            try{ $key2 = $node->filter("td:nth-child(5)")->text(); } catch (Exception $e) {}
            try{ $value2 = $node->filter("td:nth-child(7)")->text(); } catch (Exception $e) {}
            $_val2 = $null;
            if($key2 && $value2){
                $_val2 = [$key2, $value2];
            }
            return [$_val1, $_val2];
        }
    });
    $cleanAttributes = [];
    foreach ($studentInfo as &$value) {
        if($value){
            foreach ($value as &$val) {
                if($val){
                    $cleanAttributes[] = $val;
                }
            }
        }
    }
    $result['success'] = "true";
    $result['data'] = $cleanAttributes;
    $result['message'] = "success";
    return $this->response->withJson($result,200,JSON_UNESCAPED_UNICODE);
});
$app->post('/announcements', function(Request $request, Response $response, array $args) use ($app) {
    $input = $request->getParsedBody();
    $client = new Client(['cookies' => true]);
    $username = $input['txt_username'];
    $password = $input['txt_password'];
    $crawler = $client->request('GET', 'http://mymancosa.com/login.php');
    $form = $crawler->selectButton('Login')->form();
    $crawler = $client->submit($form, array('txt_username' => $username, 'txt_password' => $password, 'category' => 'Student'));
    $anoncementCrawler = $client->request('GET', 'http://mymancosa.com/anouncementDisplay.php');
    $announcements = $anoncementCrawler->filter('span')->each(function ($node) {
       return $node->text();
    });
    $result['success'] = "true";
    $result['data'] = $announcements;
    $result['message'] = "success";
    return $this->response->withJson($result,200,JSON_UNESCAPED_UNICODE);
});
$app->post('/assignments/calendar', function(Request $request, Response $response, array $args) use ($app) {
    $input = $request->getParsedBody();
    $username = $input['txt_username'];
    $password = $input['txt_password'];
    //$client = new Client();
    $client = new Client(['cookies' => true]);
    $crawler = $client->request('GET', 'http://mymancosa.com/login.php');
    $form = $crawler->selectButton('Login')->form();
    $crawler = $client->submit($form, array('txt_username' => $username, 'txt_password' => $password, 'category' => 'Student'));
    $link = $crawler->selectLink('Assignment Due Dates')->link();
    $crawler = $client->click($link);
    
    $assignmentsDueDate = $crawler->filter('#table tr')->each(function ($node, $i) {
        if($i>=1){
            $ProgrammeName = $null; 
            $Intake = $null; 
            $Year = $null; 
            $AssignmentNo = $null; 
            $ModuleName = $null; 
            $DueDate = $null; 
            $Remark = $null;
            //Programme Name	Intake	Year	Assignment No	Module Name	Due Date	Remark
            try{ $ProgrammeName = $node->filter("td:nth-child(1)")->text(); } catch (Exception $e) {}
            try{ $Intake = $node->filter("td:nth-child(2)")->text(); } catch (Exception $e) {}
            try{ $Year = $node->filter("td:nth-child(3)")->text(); } catch (Exception $e) {}
            try{ $AssignmentNo = $node->filter("td:nth-child(4)")->text(); } catch (Exception $e) {}
            try{ $ModuleName = $node->filter("td:nth-child(5)")->text(); } catch (Exception $e) {}
            try{ $DueDate = $node->filter("td:nth-child(6)")->text(); } catch (Exception $e) {}
            try{ $Remark = $node->filter("td:nth-child(7) > font")->text(); } catch (Exception $e) {}
            
            return array("ProgrammeName"=> $ProgrammeName,
                        "Intake"=> $Intake,
                        "Year"=> $Year,
                        "AssignmentNo"=> $AssignmentNo,
                        "ModuleName"=> $ModuleName,
                        "DueDate"=> $DueDate,
                        "Remark"=> $Remark);
        }
    });
    $cleanAttributes = [];
    foreach ($assignmentsDueDate as &$value) {
        if($value){
            $cleanAttributes[] = $value;
        }
    }
    $result['success'] = "true";
    $result['data'] = $cleanAttributes;
    $result['message'] = "success";
    return $this->response->withJson($result,200,JSON_UNESCAPED_UNICODE);
});
$app->post('/books', function(Request $request, Response $response, array $args) use ($app) {
    $input = $request->getParsedBody();
    $username = $input['txt_username'];
    $password = $input['txt_password'];
    //$client = new Client();
    $client = new Client(['cookies' => true]);
    $crawler = $client->request('GET', 'http://mymancosa.com/login.php');
    $form = $crawler->selectButton('Login')->form();
    $crawler = $client->submit($form, array('txt_username' => $username, 'txt_password' => $password, 'category' => 'Student'));
    $link = $crawler->selectLink('Add/View Second Hand Books')->link();
    $crawler = $client->click($link);

    $assignmentsDueDate = $crawler->filter('#table tr')->each(function ($node, $i) {
        if($i>=1){
            //S.No.	Subject Name	Book Title	Author Name	Publications	View
            $S_No = $null; 
            $SubjectName = $null; 
            $BookTitle = $null; 
            $AuthorName = $null; 
            $Publications = $null; 
            $View = $null; 
            
            try{ $S_No = $node->filter("td:nth-child(1)")->text(); } catch (Exception $e) {}
            try{ $SubjectName = $node->filter("td:nth-child(2)")->text(); } catch (Exception $e) {}
            try{ $BookTitle = $node->filter("td:nth-child(3)")->text(); } catch (Exception $e) {}
            try{ $AuthorName = $node->filter("td:nth-child(4)")->text(); } catch (Exception $e) {}
            try{ $Publications = $node->filter("td:nth-child(5)")->text(); } catch (Exception $e) {}
            try{ $View = $node->filter("td:nth-child(6) a")->extract(array('onclick')); 
                $url = str_replace("cjPopUp('","",$View[0]);
                $cleanUrl = str_replace("','Info')","",$url);
            } catch (Exception $e) {}
            
            return array("S_No"=> $S_No,
                        "SubjectName"=> $SubjectName,
                        "BookTitle"=> $BookTitle,
                        "AuthorName"=> $AuthorName,
                        "Publications"=> $Publications,
                        "View"=> $cleanUrl);
        }
    });
    $cleanAttributes = [];
    foreach ($assignmentsDueDate as &$value) {
        if($value){
            $cleanAttributes[] = $value;
        }
    }
    $result['success'] = "true";
    $result['data'] = $cleanAttributes;
    $result['message'] = "success";
    return $this->response->withJson($result,200,JSON_UNESCAPED_UNICODE);
});
$app->post('/books/view', function(Request $request, Response $response, array $args) use ($app) {
    $input = $request->getParsedBody();
    $username = $input['txt_username'];
    $password = $input['txt_password'];
    $view = $input['view'];
    //$client = new Client();
    $client = new Client(['cookies' => true]);
    $crawler = $client->request('GET', 'http://mymancosa.com/login.php');
    $form = $crawler->selectButton('Login')->form();
    $crawler = $client->submit($form, array('txt_username' => $username, 'txt_password' => $password, 'category' => 'Student'));
    $bookUrl = 'http://mymancosa.com/'.$view;
    $bookdetailsCrawler = $client->request('GET', $bookUrl);
    $bookdetails = $bookdetailsCrawler->filter('#table tr')->each(function ($node, $i) {
        if($i>=1){
            return [$node->filter("td:nth-child(1) strong")->text(), $node->filter("td:nth-child(3)")->text()];
        }
    });
    $cleanAttributes = [];
    foreach ($bookdetails as &$value) {
        if($value){
            $cleanAttributes[] = $value;
        }
    }
    $result['success'] = "true";
    $result['data'] = $cleanAttributes;
    $result['message'] = "success";
    return $this->response->withJson($result,200,JSON_UNESCAPED_UNICODE);
});
$app->post('/books/add', function(Request $request, Response $response, array $args) use ($app) {
    $input = $request->getParsedBody();
    $username = $input['txt_username'];
    $password = $input['txt_password'];
    $bookTitle = $input['bookTitle'];
    $author = $input['author'];
    $publications = $input['publications'];
    $edition = $input['edition'];
    $status = $input['status'];
    $expectedSellingPrice = $input['expectedSellingPrice'];
    $contactNumber = $input['contactNumber'];

    $client = new Client(['cookies' => true]);
    $crawler = $client->request('GET', 'http://mymancosa.com/login.php');
    $form = $crawler->selectButton('Login')->form();
    $crawler = $client->submit($form, array('txt_username' => $username, 'txt_password' => $password, 'category' => 'Student'));
    $announcementClient = $client->get('http://mymancosa.com/anouncementDisplay.php');
    $anoncementCrawler = $announcementClient->getBody();
    //http://mymancosa.com/anouncementDisplay.php
    $announcements = array();
    $anoncementCrawler->filter('span')->each(function ($node) {
        array_push($announcements, $node->text());
    });

    $result['success'] = "true";
    $result['data'] = $announcements;
    $result['message'] = "success";
    return $this->response->withJson($result,200,JSON_UNESCAPED_UNICODE);
});




$app->get('/test', function (Request $request, Response $response, array $args) {
    //health check endpoint
    $client = new Client(['cookies' => true]);
    $crawler = $client->request('GET', 'http://mymancosa.com/login.php');
    $form = $crawler->selectButton('Login')->form();
    $crawler = $client->submit($form, array('txt_username' => '147831', 'txt_password' => '147831', 'category' => 'Student'));
    $link = $crawler->selectLink('My Info')->link();
    $crawler = $client->click($link);
    
    $studentInfo = $crawler->filter('#table tr')->each(function ($node, $i) {
        if($i>=1){
            $key1 = $null; 
            $value1 = $null;
            try{ $key1 = $node->filter("td:nth-child(1)")->text(); } catch (Exception $e) {}
            try{ $value1 = $node->filter("td:nth-child(3)")->text(); } catch (Exception $e) {}
            $_val1 = $null;
            if($key1 && $value1){
                $_val1 = [$key1, $value1];
            }

            $key2 = $null; 
            $value2 = $null;
            try{ $key2 = $node->filter("td:nth-child(5)")->text(); } catch (Exception $e) {}
            try{ $value2 = $node->filter("td:nth-child(7)")->text(); } catch (Exception $e) {}
            $_val2 = $null;
            if($key2 && $value2){
                $_val2 = [$key2, $value2];
            }
            return [$_val1, $_val2];
        }
    });
    $cleanAttributes = [];
    foreach ($studentInfo as &$value) {
        if($value){
            foreach ($value as &$val) {
                if($val){
                    $cleanAttributes[] = $val;
                }
            }
        }
    }
    $result['success'] = "true";
    $result['data'] = $cleanAttributes;
    $result['message'] = "success";
    return $this->response->withJson($result,200,JSON_UNESCAPED_UNICODE);
});
