<?php
namespace Pipedrive;

use \Slim\Http\Response;
use \Slim\Http\Request;

class OrganizationController
{
    private $container;
    private $db;
    private $errors;
    private $relations;

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
        $this->db = $this->container->get('db');
        $this->errors = [];
        $this->relations = [];
    }

    public function createRelations(Request $request, Response $response, $args)
    {
        $payload = $request->getParsedBody();

        if (is_null($payload)) {
            return $response->withJson(['errors' => json_last_error_msg()], Constant::INVALID_REQUEST, JSON_PRETTY_PRINT);
        }

        $this->handlePayload($payload);
        $this->addRelations();

        if ($this->errors) {
            return $response->withJson(['errors' => $this->errors], Constant::SERVER_ERROR, JSON_PRETTY_PRINT);
        }
        return $response->withJson(['result' => 'Organizations and relations were created.'], Constant::CREATED, JSON_PRETTY_PRINT);
    }

    protected function handlePayload($payload)
    {
        $organizationId = $this->addOrganization($payload['org_name']);
        if (array_key_exists('daughters', $payload) && is_array($payload['daughters'])) {
            foreach ($payload['daughters'] as $daughter) {
                $childId = $this->handlePayload($daughter);
                $this->addRelation(['organization_id' => $childId, 'parent_id' => $organizationId]);
                if (count($this->relations) >= 100) {
                    $this->addRelations();
                }
            }
        }
        return $organizationId;
    }

    protected function addOrganization($name)
    {
        $id = $this->getOrganization($name);
        if (empty($id)) {
            $this->db->insert('organizations', ['name' => $name]);
            $id = $this->db->id();
        }
        return $id;
    }

    protected function addRelations()
    {
        if (empty($this->relations)) {
            return false;
        }
        $this->db->insert('relations', $this->relations);
    }

    protected function addRelation($data)
    {
        if (!in_array($data, $this->relations)) {
            $this->relations[] = $data;
        }
    }

    protected function getOrganization($name)
    {
        return $this->db->get('organizations', 'id', ['name' => $name]);
    }

    public function getRelations(Request $request, Response $response)
    {
        $name = $request->getAttribute('org_name');
        $page = (int)$request->getQueryParams()['page'];

        $id = $this->getOrganization($name);
        $relations = $this->getAllRelations($id, $page);
        if ($relations === false) {
            return $response->withJson(['error' => 'Could not get relations from DB'], Constant::SERVER_ERROR, JSON_PRETTY_PRINT);
        }

        $code = count($relations) ? Constant::SUCCESS : Constant::NO_CONTENT;
        return $response->withJson($relations, $code, JSON_PRETTY_PRINT);
    }

    protected function getAllRelations($id, $page)
    {
        $sql = "SELECT 'parent' AS relationship_type, o.name AS org_name
                FROM relations r
                JOIN organizations o ON r.`parent_id` = o.id
                WHERE r.`organization_id` = :id
                UNION SELECT 'daughter' AS relationship_type, o2.name AS org_name
                FROM relations r2
                JOIN organizations o2 ON r2.organization_id = o2.id
                WHERE r2.parent_id = :id
                UNION SELECT 'sister' AS relationship_type, o3.name AS org_name
                FROM relations r3
                JOIN organizations o3 ON r3.`organization_id` = o3.id
                WHERE r3.`parent_id` IN (SELECT `parent_id` FROM relations WHERE organization_id = :id)
                ORDER BY org_name ASC
                LIMIT :start,:limit";
        return $this->db->query($sql,[':id' => $id, ':start' => $page * Constant::LIMIT, ':limit' => Constant::LIMIT])
            ->fetchAll(\PDO::FETCH_ASSOC);
    }
}
