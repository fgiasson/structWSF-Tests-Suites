<?php

  namespace StructuredDynamics\structwsf\tests\ws\ontology\delete;
  
  use StructuredDynamics\structwsf\framework\WebServiceQuerier;
  use StructuredDynamics\structwsf\tests\Config;
  use StructuredDynamics\structwsf\php\api\ws\ontology\delete\OntologyDeleteQuery;
  use StructuredDynamics\structwsf\php\api\ws\ontology\read\OntologyReadQuery;
  use StructuredDynamics\structwsf\php\api\ws\ontology\read\GetPropertyFunction;
  use StructuredDynamics\structwsf\php\api\ws\ontology\read\GetClassFunction;
  use StructuredDynamics\structwsf\php\api\ws\ontology\read\GetNamedIndividualFunction;
  use StructuredDynamics\structwsf\php\api\ws\ontology\delete\DeleteClassFunction;
  use StructuredDynamics\structwsf\php\api\ws\ontology\delete\DeleteNamedIndividualFunction;
  use StructuredDynamics\structwsf\php\api\ws\ontology\delete\DeletePropertyFunction;
  use StructuredDynamics\structwsf\tests as utilities;
   
  include_once("SplClassLoader.php");
  include_once("validators.php");
  include_once("utilities.php");   
  
  // Load the \tests namespace where all the test code is located 
  $loader_tests = new \SplClassLoader('StructuredDynamics\structwsf\tests', realpath("../../../"));
  $loader_tests->register();
 
  // Load the \ws namespace where all the web service code is located 
  $loader_ws = new \SplClassLoader('StructuredDynamics\structwsf\php\api\ws', realpath("../../../"));
  $loader_ws->register();  
  
  // Load the \php\api\framework namespace where all the web service code is located 
  $loader_ws = new \SplClassLoader('StructuredDynamics\structwsf\php\api\framework', realpath("../../../"));
  $loader_ws->register();  
 
  // Load the \framework namespace where all the supporting (utility) code is located
  $loader_framework = new \SplClassLoader('StructuredDynamics\structwsf\framework', realpath("../../../"));
  $loader_framework->register(); 
  
  ini_set("memory_limit","256M");
  set_time_limit(3600);

  $settings = new Config(); 
  
  class OntologyDeleteTest extends \PHPUnit_Framework_TestCase {
    
    static private $outputs = array();
    
    public function testWrongEndpointUrl() {
      
      $settings = new Config();          
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/" . "wrong", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteOntology") .
                                   "&parameters=" . urlencode("") .
                                   "&registered_ip=" . urlencode("self"));
                   
      $this->assertEquals($wsq->getStatus(), "404", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Not Found", "Debugging information: ".var_export($wsq, TRUE));
      
      unset($wsq);
      unset($settings);
    }
    
    public function testWrongEndpointMethodGet() {
      
      $settings = new Config();  
      
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "get", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteOntology") .
                                   "&parameters=" . urlencode("") .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "405", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Method Not Allowed", "Debugging information: ".var_export($wsq, TRUE));          
      
      unset($wsq);
      unset($settings);
    }    
    
    public function  testDeleteOntology_unknownFunctionCall() {
      
      $settings = new Config();  
      
      // Delete Ontology
      $wsq = new WebServiceQuerier($settings->endpointUrl . "ontology/delete/", 
                                   "post", 
                                   "text/xml",
                                   "ontology=" . urlencode($settings->testOntologyUri) .
                                   "&function=" . urlencode("deleteOntology" . "unknown") .
                                   "&parameters=" . urlencode("") .
                                   "&registered_ip=" . urlencode("self"));
                                   
      $this->assertEquals($wsq->getStatus(), "400", "Debugging information: ".var_export($wsq, TRUE));                                       
      $this->assertEquals($wsq->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($wsq, TRUE));
      $this->assertEquals($wsq->error->id, "WS-ONTOLOGY-DELETE-200", "Debugging information: ".var_export($wsq, TRUE));    
                                    
      unset($wsq);
      unset($settings);
    }   
    
    public function testValidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $ontologyDelete->deleteOntology();
      
      $ontologyDelete->sourceInterface("default");
      
      $ontologyDelete->sourceInterfaceVersion($settings->ontologyDeleteInterfaceVersion);
      
      $ontologyDelete->send();
                           
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       

      utilities\deleteDataset();

      unset($ontologyDelete);
      unset($settings);   
    }
    
    
    public function testInvalidInterfaceVersion() {
      
      $settings = new Config();  

      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $ontologyDelete->deleteOntology();
      
      $ontologyDelete->sourceInterface("default");
      
      $ontologyDelete->sourceInterfaceVersion("667.4");
      
      $ontologyDelete->send();
                           
      $this->assertEquals($ontologyDelete->getStatus(), "400", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       
      $this->assertEquals($ontologyDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyDelete, TRUE));
      $this->assertEquals($ontologyDelete->error->id, "WS-ONTOLOGY-DELETE-302", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       

      utilities\deleteDataset();

      unset($ontologyDelete);
      unset($settings);                              
    }    
    
    //
    // Test existing interface
    //
    
    public function testInterfaceExists() {
      
      $settings = new Config();  

      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $ontologyDelete->deleteOntology();
      
      $ontologyDelete->sourceInterface("default");
      
      $ontologyDelete->send();
                           
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       

      utilities\deleteDataset();

      unset($ontologyDelete);
      unset($settings);
    }  
    
    //
    // Test unexisting interface
    //
    
    public function testInterfaceNotExisting() {
      
      $settings = new Config();  

      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $ontologyDelete->deleteOntology();
      
      $ontologyDelete->sourceInterface("default-not-existing");
      
      $ontologyDelete->send();
                           
      $this->assertEquals($ontologyDelete->getStatus(), "400", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       
      $this->assertEquals($ontologyDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyDelete, TRUE));
      $this->assertEquals($ontologyDelete->error->id, "WS-ONTOLOGY-DELETE-301", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       

      utilities\deleteDataset();

      unset($ontologyDelete);
      unset($settings);
    }     
    
    public function  testDeleteOntology_NoOntologyUriSpecified() {
      
      $settings = new Config();  
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology("");
      
      $ontologyDelete->deleteOntology();
      
      $ontologyDelete->send();
                                         
      $this->assertEquals($ontologyDelete->getStatus(), "400", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       
      $this->assertEquals($ontologyDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyDelete, TRUE));
      $this->assertEquals($ontologyDelete->error->id, "WS-ONTOLOGY-DELETE-201", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      unset($settings);
    }       

    public function  testDeleteOntology_NoPropertyUriSpecified() {
      
      $settings = new Config();  
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deletePropertyFunction = new DeletePropertyFunction();
      
      $deletePropertyFunction->uri("");
      
      $ontologyDelete->deleteProperty($deletePropertyFunction);
      
      $ontologyDelete->send();
                                   
      $this->assertEquals($ontologyDelete->getStatus(), "400", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       
      $this->assertEquals($ontologyDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyDelete, TRUE));
      $this->assertEquals($ontologyDelete->error->id, "WS-ONTOLOGY-DELETE-202", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      unset($settings);
    } 

    public function  testDeleteOntology_NoNamedIndividualUriSpecified() {
      
      $settings = new Config();  

      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deleteNamedIndividualFunction = new DeleteNamedIndividualFunction();
      
      $deleteNamedIndividualFunction->uri("");
      
      $ontologyDelete->deleteNamedIndividual($deleteNamedIndividualFunction);
      
      $ontologyDelete->send();      
                                   
      $this->assertEquals($ontologyDelete->getStatus(), "400", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       
      $this->assertEquals($ontologyDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyDelete, TRUE));
      $this->assertEquals($ontologyDelete->error->id, "WS-ONTOLOGY-DELETE-203", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      unset($settings);
    }         

    public function  testDeleteOntology_NoClassUriSpecified() {
      
      $settings = new Config();  
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deleteClassFunction = new DeleteClassFunction();
      
      $deleteClassFunction->uri("");
      
      $ontologyDelete->deleteClass($deleteClassFunction);
      
      $ontologyDelete->send();      
                                   
      $this->assertEquals($ontologyDelete->getStatus(), "400", "Debugging information: ".var_export($ontologyDelete, TRUE));                                       
      $this->assertEquals($ontologyDelete->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyDelete, TRUE));
      $this->assertEquals($ontologyDelete->error->id, "WS-ONTOLOGY-DELETE-204", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      unset($settings);
    } 
        
    public function  testDeleteOntology_function_deleteOntology() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $ontologyDelete->deleteOntology();
      
      $ontologyDelete->send();
                                   
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($wsq);
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);
      
      $ontologyRead->mime("application/rdf+xml");
      
      $ontologyRead->ontology($settings->testOntologyUri);
      
      $ontologyRead->getSerialized();
      
      $ontologyRead->enableReasoner();

      $ontologyRead->send();             

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($ontologyRead->getStatus(), "403", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Forbidden", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-AUTH-VALIDATOR-303", "Debugging information: ".var_export($ontologyRead, TRUE));    

      unset($ontologyRead);      
      unset($settings);
    } 
    
    public function  testDeleteOntology_function_deleteProperty_Datatype() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      

      
      // Delete Ontology
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deletePropertyFunction = new DeletePropertyFunction();
      
      $deletePropertyFunction->uri($settings->targetDatatypePropertyUri);
      
      $ontologyDelete->deleteProperty($deletePropertyFunction);
      
      $ontologyDelete->send();      
      
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);
      
      $ontologyRead->mime("application/rdf+xml");
      
      $ontologyRead->ontology($settings->testOntologyUri);
      
      $getPropertyFunction = new GetPropertyFunction();
      
      $getPropertyFunction->uri($settings->targetDatatypePropertyUri);
      
      $ontologyRead->getProperty($getPropertyFunction);
      
      $ontologyRead->enableReasoner();

      $ontologyRead->send();        
      
      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($ontologyRead->getStatus(), "400", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-ONTOLOGY-READ-204", "Debugging information: ".var_export($ontologyRead, TRUE));    

      unset($ontologyRead);      
      
      utilities\deleteOntology();      
      
      unset($settings);
    }     
    
    public function  testDeleteOntology_function_deleteProperty_Object() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deletePropertyFunction = new DeletePropertyFunction();
      
      $deletePropertyFunction->uri($settings->targetObjectPropertyUri);
      
      $ontologyDelete->deleteProperty($deletePropertyFunction);
      
      $ontologyDelete->send();       
                                   
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      
      // Make sure it is deleted      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);
      
      $ontologyRead->mime("application/rdf+xml");
      
      $ontologyRead->ontology($settings->testOntologyUri);
      
      $getPropertyFunction = new GetPropertyFunction();
      
      $getPropertyFunction->uri($settings->targetObjectPropertyUri);
      
      $ontologyRead->getProperty($getPropertyFunction);
      
      $ontologyRead->enableReasoner();

      $ontologyRead->send();             

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($ontologyRead->getStatus(), "400", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-ONTOLOGY-READ-204", "Debugging information: ".var_export($ontologyRead, TRUE));    

      unset($ontologyRead);      
      
      utilities\deleteOntology();      
      
      unset($settings);
    }   
      
    public function  testDeleteOntology_function_deleteProperty_Annotation() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deletePropertyFunction = new DeletePropertyFunction();
      
      $deletePropertyFunction->uri($settings->targetAnnotationPropertyUri);
      
      $ontologyDelete->deleteProperty($deletePropertyFunction);
      
      $ontologyDelete->send();        
                                   
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      
      // Make sure it is deleted     
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);
      
      $ontologyRead->mime("application/rdf+xml");
      
      $ontologyRead->ontology($settings->testOntologyUri);
      
      $getPropertyFunction = new GetPropertyFunction();
      
      $getPropertyFunction->uri($settings->targetAnnotationPropertyUri);
      
      $ontologyRead->getProperty($getPropertyFunction);
      
      $ontologyRead->enableReasoner();

      $ontologyRead->send();    

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($ontologyRead->getStatus(), "400", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-ONTOLOGY-READ-204", "Debugging information: ".var_export($ontologyRead, TRUE));    

      unset($ontologyRead);      
      
      utilities\deleteOntology();      
      
      unset($settings);
    }
    
    public function  testDeleteOntology_function_deleteClass() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deleteClassFunction = new DeleteClassFunction();
      
      $deleteClassFunction->uri($settings->targetClassUri);
      
      $ontologyDelete->deleteClass($deleteClassFunction);
      
      $ontologyDelete->send();        
      
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      
      // @TODO For some reason, the Named Individual is not in the ontology anymore, it it is still returned
      // by the getOWLClass() API call when we execute this code. Need some more debugging to figure out
      // why this happens, and by getOWLClass() is not returning null.      
      
      // Make sure it is deleted      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);
      
      $ontologyRead->mime("application/rdf+xml");
      
      $ontologyRead->ontology($settings->testOntologyUri);
      
      $getClassFunction = new GetClassFunction();
      
      $getClassFunction->uri($settings->targetClassUri);
      
      $ontologyRead->getClass($getClassFunction);
      
      $ontologyRead->enableReasoner();

      $ontologyRead->send();  
         
      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($ontologyRead->getStatus(), "400", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-ONTOLOGY-READ-205", "Debugging information: ".var_export($ontologyRead, TRUE));    

      unset($ontologyRead);      
      
      utilities\deleteOntology();      
      
      unset($settings);
    }    
     
    public function  testDeleteOntology_function_deleteNamedIndividual() {
      
      $settings = new Config();  
      
      utilities\deleteOntology();
      
      $this->assertTrue(utilities\createOntology(), "Can't create the ontology, check the /ontology/create/ endpoint first...");
      
      // Delete Ontology
      $ontologyDelete = new OntologyDeleteQuery($settings->endpointUrl);
      
      $ontologyDelete->ontology($settings->testOntologyUri);
      
      $deleteNamedIndividualFunction = new DeleteNamedIndividualFunction();
      
      $deleteNamedIndividualFunction->uri($settings->targetNamedIndividualUri);
      
      $ontologyDelete->deleteNamedIndividual($deleteNamedIndividualFunction);
      
      $ontologyDelete->send();        
                                         
      $this->assertEquals($ontologyDelete->getStatus(), "200", "Debugging information: ".var_export($ontologyDelete, TRUE));    
                                    
      unset($ontologyDelete);
      
      // @TODO For some reason, the Named Individual is not in the ontology anymore, it it is still returned
      // by the getOWLNamedIndividal() API call when we execute this code. Need some more debugging to figure out
      // why this happens, and by getOWLNamedIndividal() is not returning null.
      
      // Make sure it is deleted      
      $ontologyRead = new OntologyReadQuery($settings->endpointUrl);
      
      $ontologyRead->mime("application/rdf+xml");
      
      $ontologyRead->ontology($settings->testOntologyUri);
      
      $getNamedIndividualFunction = new GetNamedIndividualFunction();
      
      $getNamedIndividualFunction->uri($settings->targetNamedIndividualUri);
      
      $ontologyRead->getNamedIndividual($getNamedIndividualFunction);
      
      $ontologyRead->enableReasoner();

      $ontologyRead->send();        

      // Since the ontology is not existing anymore, there is not auth information, so it means it as been
      // properly deleted.                                   
      $this->assertEquals($ontologyRead->getStatus(), "400", "Debugging information: ".var_export($ontologyRead, TRUE));                                       
      $this->assertEquals($ontologyRead->getStatusMessage(), "Bad Request", "Debugging information: ".var_export($ontologyRead, TRUE));
      $this->assertEquals($ontologyRead->error->id, "WS-ONTOLOGY-READ-206", "Debugging information: ".var_export($ontologyRead, TRUE));    

      unset($ontologyRead);      
      
      utilities\deleteOntology();      
      
      unset($settings);
    }     
  }

  
?>