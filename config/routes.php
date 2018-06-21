<?php

$app->get('/api/v1/organizations/{org_name}', \Pipedrive\OrganizationController::class . ':getRelations');
$app->post('/api/v1/organizations', \Pipedrive\OrganizationController::class . ':createRelations');
