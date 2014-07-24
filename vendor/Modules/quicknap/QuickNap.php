<?php

class QuickNap
{

    public function __construct()
    {
        $this->app = new \Slim();

        $this->client = new \Client();
        $this->cpush = new\CPush();

        $this->patient = new \Patient();
        $this->ppush = new \PPush();

        $this->catchAll = new \CatchAll();

        $this->clinic = new \Clinic();
    }

    public function enable()
    {
        // setup the routes for catch all
        $this->app->get('/', array($this, 'catchAll'));
        $this->app->post('/', array($this, 'catchAll'));
        $this->app->put('/', array($this, 'catchAll'));
        $this->app->delete('/', array($this, 'catchAll'));

        // setup routes for client push
        $this->app->get('/v1/client/push', array($this, 'getClientsPush'));
        $this->app->get('/v1/client/push/:id', array($this, 'getClientPushById'));
        $this->app->post('/v1/client/push/post/:id', array($this, 'postClientById'));
        //$this->app->put('/v1/client/put/:id', array($this, 'putClientById'));
        $this->app->delete('/v1/client/push/delete/:id', array($this, 'deleteClientById'));

        //setup routes for patients push
        $this->app->get('/v1/patient/push', array($this, 'getPatientsPush'));
        $this->app->get('/v1/patient/push/:id', array($this, 'getPatientPushById'));
        $this->app->post('/v1/patient/push/post/:id', array($this, 'postPatientPushById'));
        //$this->app->put('/v1/patient/put/:id', array($this, 'putPatientById'));
        $this->app->delete('/v1/patient/push/delete/:id', array($this, 'deletePatientPushById'));

        //setup routes for clients
        $this->app->get('/v1/client/:dsid', array($this, 'getClients'));
        $this->app->get('/v1/client/:dsid/:id', array($this, 'getClientById'));

        //setup routes for patients
        $this->app->get('/v1/patient/:dsid', array($this, 'getAllPatients'));
        $this->app->get('/v1/patient/:dsid/:rid', array($this, 'getPatientById'));

        //setup routes for appointments

        //setup routes for reminders

        //setup routes for PIMS
        $this->app->post('/v1/clinic/push', array($this, 'postClinicById'));

        // start Slim
        $this->app->run();
    }

    public function catchAll()
    {
        $this->catchAll->catchAll();
    }

    public function getClientsPush()
    {
        $this->cpush->getClients();
    }

    public function getClientPushById($id)
    {
        $this->cpush->getClientById($id);
    }

    public function postClientById($id)
    {
        $this->cpush->postClientById($id);
    }

    public function deleteClientById($id)
    {
        $this->cpush->deleteClientById($id);
    }

    public function putClientById($id)
    {
        $this->cpush->putClientById($id);
    }

    public function getPatientsPush()
    {
        $this->ppush->getAllPatient();
    }

    public function getPatientPushById($id)
    {
        $this->ppush->getPatient($id);
    }

    public function postPatientPushById($id)
    {
        $this->ppush->postPatient($id);
    }

    public function deletePatientPushById($id)
    {
        $this->ppush->deletePatient($id);
    }

    public function putPatient($id)
    {
        $this->ppush->putPatient($id);
    }

    public function getClients($dsid)
    {
        $this->client->getClients($dsid);
    }

    public function getClientById($dsid, $id)
    {
        $this->client->getClientById($dsid, $id);
    }

    public function getAllPatients($dsid)
    {
        $this->patient->getAllPatient($dsid);
    }

    public function getPatientById($dsid, $rid)
    {
        $this->patient->getPatientByRid($dsid, $rid);
    }

    public function postClinicById()
    {
        $this->clinic->postClinicById();
    }

}