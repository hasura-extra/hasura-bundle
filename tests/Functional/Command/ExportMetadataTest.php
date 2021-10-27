<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Functional\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class ExportMetadataTest extends KernelTestCase
{
    use CleanupMetadataPathTrait;

    public function testExportMetadata(): void
    {
        $kernel = self::bootKernel();
        $path = sprintf('%s/hasura', $kernel->getProjectDir());
        $tester = new CommandTester((new Application($kernel))->find('hasura:metadata:export'));
        $tester->execute([]);

        $this->assertStringContainsString('Exporting...', $tester->getDisplay());
        $this->assertStringContainsString('Done!', $tester->getDisplay());

        $this->assertStringEqualsFile(
            $path . '/actions.yaml',
            <<<'ACTIONS'
- !include actions/insert_product.yaml

ACTIONS
        );
        $this->assertStringEqualsFile(
            $path . '/actions/insert_product.yaml',
            <<<'ACTION_INSERT_PRODUCT'
name: insertProduct
definition:
    handler: 'http://localhost/hasura_action'
    output_type: InsertProductOutput
    arguments:
        -
            name: object
            type: InsertProductInput!
    type: mutation
    kind: synchronous
request_transform: ~

ACTION_INSERT_PRODUCT
        );

        $this->assertStringEqualsFile(
            $path . '/allow_list.yaml',
            <<<'ALLOW_LIST'
-
    collection: allowed-queries

ALLOW_LIST
        );

        $this->assertStringEqualsFile(
            $path . '/custom_types.yaml',
            <<<'CUSTOM_TYPES'
input_objects: !include custom_types/input_objects.yaml
objects: !include custom_types/objects.yaml

CUSTOM_TYPES
        );
        $this->assertStringEqualsFile(
            $path . '/custom_types/objects.yaml',
            <<<'CUSTOM_TYPES_OBJECTS'
- !include objects/insert_product_output.yaml

CUSTOM_TYPES_OBJECTS
        );
        $this->assertStringEqualsFile(
            $path . '/custom_types/input_objects.yaml',
            <<<'CUSTOM_TYPES_OBJECTS'
- !include input_objects/insert_product_input.yaml

CUSTOM_TYPES_OBJECTS
        );
        $this->assertStringEqualsFile(
            $path . '/custom_types/input_objects/insert_product_input.yaml',
            <<<'CUSTOM_TYPES_OBJECT_INSERT_PRODUCT_INPUT'
name: InsertProductInput
fields:
    -
        name: name
        type: String!

CUSTOM_TYPES_OBJECT_INSERT_PRODUCT_INPUT
        );
        $this->assertStringEqualsFile(
            $path . '/custom_types/objects/insert_product_output.yaml',
            <<<'CUSTOM_TYPES_OBJECT_INSERT_PRODUCT_OUTPUT'
name: InsertProductOutput
fields:
    -
        name: id
        type: uuid!

CUSTOM_TYPES_OBJECT_INSERT_PRODUCT_OUTPUT
        );

        $this->assertStringEqualsFile(
            $path . '/query_collections.yaml',
            <<<'QUERY_COLLECTIONS'
- !include query_collections/allowed_queries.yaml

QUERY_COLLECTIONS
        );
        $this->assertStringEqualsFile(
            $path . '/query_collections/allowed_queries.yaml',
            <<<'QUERY_COLLECTIONS_ALLOW_QUERIES'
name: allowed-queries
definition:
    queries:
        -
            name: 'Get Users'
            query: |-
                query GetUsers {
                  users {
                    id 
                    name
                  }
                }
        -
            name: 'Get Products'
            query: |-
                query GetProducts {
                  products {
                    id
                    name
                  }
                }
QUERY_COLLECTIONS_ALLOW_QUERIES
        );

        $this->assertStringEqualsFile(
            $path . '/remote_schemas.yaml',
            <<<'REMOTE_SCHEMAS'
-
    name: swapi
    definition:
        url: 'https://swapi-graphql.netlify.app/.netlify/functions/index'
        timeout_seconds: 60
        customization:
            type_names:
                prefix: swapi_
                mapping: {  }
            field_names:
                -
                    prefix: swapi_
                    parent_type: Root
                    mapping: {  }
    comment: ''
    permissions: !include remote_schemas/swapi/permissions.yaml

REMOTE_SCHEMAS
        );
        $this->assertStringEqualsFile(
            $path . '/remote_schemas/swapi/permissions.yaml',
            <<<'REMOTE_SCHEMAS_PERMISSIONS_SWAPI'
- !include permissions/role_manager.yaml

REMOTE_SCHEMAS_PERMISSIONS_SWAPI
        );
        $this->assertStringEqualsFile(
            $path . '/remote_schemas/swapi/permissions/role_manager.yaml',
            <<<'REMOTE_SCHEMAS_PERMISSIONS_SWAPI_ROLE_MANAGER'
role: manager
definition:
    schema: |-
        schema  { query: Root }

        type Film { characterConnection: FilmCharactersConnection
          created: String
          director: String
          edited: String
          episodeID: Int
          id: ID!
          openingCrawl: String
          planetConnection: FilmPlanetsConnection
          producers: [String]
          releaseDate: String
          speciesConnection: FilmSpeciesConnection
          starshipConnection: FilmStarshipsConnection
          title: String
          vehicleConnection: FilmVehiclesConnection
        }

        type FilmCharactersConnection { characters: [Person]
          edges: [FilmCharactersEdge]
          pageInfo: PageInfo!
          totalCount: Int
        }

        type FilmCharactersEdge { cursor: String!
          node: Person
        }

        type FilmPlanetsConnection { edges: [FilmPlanetsEdge]
          pageInfo: PageInfo!
          planets: [Planet]
          totalCount: Int
        }

        type FilmPlanetsEdge { cursor: String!
          node: Planet
        }

        type FilmSpeciesConnection { edges: [FilmSpeciesEdge]
          pageInfo: PageInfo!
          species: [Species]
          totalCount: Int
        }

        type FilmSpeciesEdge { cursor: String!
          node: Species
        }

        type FilmStarshipsConnection { edges: [FilmStarshipsEdge]
          pageInfo: PageInfo!
          starships: [Starship]
          totalCount: Int
        }

        type FilmStarshipsEdge { cursor: String!
          node: Starship
        }

        type FilmVehiclesConnection { edges: [FilmVehiclesEdge]
          pageInfo: PageInfo!
          totalCount: Int
          vehicles: [Vehicle]
        }

        type FilmVehiclesEdge { cursor: String!
          node: Vehicle
        }

        type PageInfo { endCursor: String
          hasNextPage: Boolean!
          hasPreviousPage: Boolean!
          startCursor: String
        }

        type Person { birthYear: String
          created: String
          edited: String
          eyeColor: String
          filmConnection: PersonFilmsConnection
          gender: String
          hairColor: String
          height: Int
          homeworld: Planet
          id: ID!
          mass: Float
          name: String
          skinColor: String
          species: Species
          starshipConnection: PersonStarshipsConnection
          vehicleConnection: PersonVehiclesConnection
        }

        type PersonFilmsConnection { edges: [PersonFilmsEdge]
          films: [Film]
          pageInfo: PageInfo!
          totalCount: Int
        }

        type PersonFilmsEdge { cursor: String!
          node: Film
        }

        type PersonStarshipsConnection { edges: [PersonStarshipsEdge]
          pageInfo: PageInfo!
          starships: [Starship]
          totalCount: Int
        }

        type PersonStarshipsEdge { cursor: String!
          node: Starship
        }

        type PersonVehiclesConnection { edges: [PersonVehiclesEdge]
          pageInfo: PageInfo!
          totalCount: Int
          vehicles: [Vehicle]
        }

        type PersonVehiclesEdge { cursor: String!
          node: Vehicle
        }

        type Planet { climates: [String]
          created: String
          diameter: Int
          edited: String
          filmConnection: PlanetFilmsConnection
          gravity: String
          id: ID!
          name: String
          orbitalPeriod: Int
          population: Float
          residentConnection: PlanetResidentsConnection
          rotationPeriod: Int
          surfaceWater: Float
          terrains: [String]
        }

        type PlanetFilmsConnection { edges: [PlanetFilmsEdge]
          films: [Film]
          pageInfo: PageInfo!
          totalCount: Int
        }

        type PlanetFilmsEdge { cursor: String!
          node: Film
        }

        type PlanetResidentsConnection { edges: [PlanetResidentsEdge]
          pageInfo: PageInfo!
          residents: [Person]
          totalCount: Int
        }

        type PlanetResidentsEdge { cursor: String!
          node: Person
        }

        type Root { starship(id: ID, starshipID: ID): Starship
        }

        type Species { averageHeight: Float
          averageLifespan: Int
          classification: String
          created: String
          designation: String
          edited: String
          eyeColors: [String]
          filmConnection: SpeciesFilmsConnection
          hairColors: [String]
          homeworld: Planet
          id: ID!
          language: String
          name: String
          personConnection: SpeciesPeopleConnection
          skinColors: [String]
        }

        type SpeciesFilmsConnection { edges: [SpeciesFilmsEdge]
          films: [Film]
          pageInfo: PageInfo!
          totalCount: Int
        }

        type SpeciesFilmsEdge { cursor: String!
          node: Film
        }

        type SpeciesPeopleConnection { edges: [SpeciesPeopleEdge]
          pageInfo: PageInfo!
          people: [Person]
          totalCount: Int
        }

        type SpeciesPeopleEdge { cursor: String!
          node: Person
        }

        type Starship { MGLT: Int
          cargoCapacity: Float
          consumables: String
          costInCredits: Float
          created: String
          crew: String
          edited: String
          filmConnection: StarshipFilmsConnection
          hyperdriveRating: Float
          id: ID!
          length: Float
          manufacturers: [String]
          maxAtmospheringSpeed: Int
          model: String
          name: String
          passengers: String
          pilotConnection: StarshipPilotsConnection
          starshipClass: String
        }

        type StarshipFilmsConnection { edges: [StarshipFilmsEdge]
          films: [Film]
          pageInfo: PageInfo!
          totalCount: Int
        }

        type StarshipFilmsEdge { cursor: String!
          node: Film
        }

        type StarshipPilotsConnection { edges: [StarshipPilotsEdge]
          pageInfo: PageInfo!
          pilots: [Person]
          totalCount: Int
        }

        type StarshipPilotsEdge { cursor: String!
          node: Person
        }

        type Vehicle { cargoCapacity: Float
          consumables: String
          costInCredits: Float
          created: String
          crew: String
          edited: String
          filmConnection: VehicleFilmsConnection
          id: ID!
          length: Float
          manufacturers: [String]
          maxAtmospheringSpeed: Int
          model: String
          name: String
          passengers: String
          pilotConnection: VehiclePilotsConnection
          vehicleClass: String
        }

        type VehicleFilmsConnection { edges: [VehicleFilmsEdge]
          films: [Film]
          pageInfo: PageInfo!
          totalCount: Int
        }

        type VehicleFilmsEdge { cursor: String!
          node: Film
        }

        type VehiclePilotsConnection { edges: [VehiclePilotsEdge]
          pageInfo: PageInfo!
          pilots: [Person]
          totalCount: Int
        }

        type VehiclePilotsEdge { cursor: String!
          node: Person
        }
REMOTE_SCHEMAS_PERMISSIONS_SWAPI_ROLE_MANAGER
        );

        $this->assertStringEqualsFile(
            $path . '/rest_endpoints.yaml',
            <<<'REST_ENDPOINTS'
- !include rest_endpoints/get_products.yaml
- !include rest_endpoints/get_users.yaml

REST_ENDPOINTS
        );
        $this->assertStringEqualsFile(
            $path . '/rest_endpoints/get_products.yaml',
            <<<'REST_ENDPOINTS_GET_PRODUCTS'
definition:
    query:
        collection_name: allowed-queries
        query_name: 'Get Products'
url: products
methods:
    - GET
name: 'Get Products'
comment: ~

REST_ENDPOINTS_GET_PRODUCTS
        );
        $this->assertStringEqualsFile(
            $path . '/rest_endpoints/get_users.yaml',
            <<<'REST_ENDPOINTS_GET_USERS'
definition:
    query:
        collection_name: allowed-queries
        query_name: 'Get Users'
url: users
methods:
    - GET
name: 'Get Users'
comment: ~

REST_ENDPOINTS_GET_USERS
        );

        $this->assertStringEqualsFile(
            $path . '/sources.yaml',
            <<<'SOURCES'
-
    name: default
    kind: postgres
    tables: !include sources/default/tables.yaml
    configuration:
        connection_info:
            use_prepared_statements: true
            database_url:
                from_env: HASURA_GRAPHQL_DATABASE_URL
            isolation_level: read-committed
            pool_settings:
                connection_lifetime: 600
                retries: 1
                idle_timeout: 180
                max_connections: 50

SOURCES
        );
        $this->assertStringEqualsFile(
            $path . '/sources/default/tables.yaml',
            <<<'SOURCES_DEFAULT_TABLES'
- !include tables/public_product_users.yaml
- !include tables/public_products.yaml
- !include tables/public_users.yaml

SOURCES_DEFAULT_TABLES
        );
        $this->assertStringEqualsFile(
            $path . '/sources/default/tables/public_users.yaml',
            <<<'SOURCES_DEFAULT_TABLES_PUBLIC_USERS'
table:
    schema: public
    name: users
array_relationships:
    -
        name: product_users
        using:
            foreign_key_constraint_on:
                column: user_id
                table:
                    schema: public
                    name: product_users
insert_permissions:
    -
        role: manager
        permission:
            check: {  }
            columns:
                - id
                - name
                - email
                - created_at
            backend_only: false
select_permissions:
    -
        role: manager
        permission:
            columns:
                - email
                - name
                - created_at
                - id
            filter: {  }
    -
        role: user
        permission:
            columns:
                - id
                - name
                - email
                - created_at
            filter:
                id:
                    _eq: X-Hasura-User-Id
update_permissions:
    -
        role: manager
        permission:
            columns:
                - email
                - name
                - created_at
                - id
            filter: {  }
            check: {  }
    -
        role: user
        permission:
            columns: []
            filter:
                id:
                    _eq: X-Hasura-User-Id
            check: ~
delete_permissions:
    -
        role: manager
        permission:
            filter: {  }
event_triggers:
    -
        name: insertedUser
        definition:
            enable_manual: false
            insert:
                columns: '*'
        retry_conf:
            num_retries: 0
            interval_sec: 10
            timeout_sec: 60
        request_transform: ~
        webhook: 'http://localhost/hasura_event'

SOURCES_DEFAULT_TABLES_PUBLIC_USERS
        );
        $this->assertStringEqualsFile(
            $path . '/sources/default/tables/public_products.yaml',
            <<<'SOURCES_DEFAULT_TABLES_PUBLIC_PRODUCTS'
table:
    schema: public
    name: products
array_relationships:
    -
        name: product_users
        using:
            foreign_key_constraint_on:
                column: product_id
                table:
                    schema: public
                    name: product_users

SOURCES_DEFAULT_TABLES_PUBLIC_PRODUCTS
        );
        $this->assertStringEqualsFile(
            $path . '/sources/default/tables/public_product_users.yaml',
            <<<'SOURCES_DEFAULT_TABLES_PUBLIC_PRODUCTS'
table:
    schema: public
    name: product_users
object_relationships:
    -
        name: product
        using:
            foreign_key_constraint_on: product_id
    -
        name: user
        using:
            foreign_key_constraint_on: user_id
insert_permissions:
    -
        role: manager
        permission:
            check: {  }
            columns:
                - created_at
                - expired_at
                - id
                - product_id
                - user_id
            backend_only: false
    -
        role: user
        permission:
            check:
                user_id:
                    _eq: X-Hasura-User-Id
            set:
                user_id: x-hasura-User-Id
            columns:
                - id
                - user_id
                - product_id
                - expired_at
                - created_at
            backend_only: false
select_permissions:
    -
        role: manager
        permission:
            columns:
                - created_at
                - expired_at
                - id
                - product_id
                - user_id
            filter: {  }
update_permissions:
    -
        role: manager
        permission:
            columns:
                - created_at
                - expired_at
                - id
                - product_id
                - user_id
            filter: {  }
            check: {  }
delete_permissions:
    -
        role: manager
        permission:
            filter: {  }
    -
        role: user
        permission:
            filter:
                user_id:
                    _eq: X-Hasura-User-Id

SOURCES_DEFAULT_TABLES_PUBLIC_PRODUCTS
        );
    }

    public function testReExportMetadataRemoved(): void
    {
        $kernel = self::bootKernel();
        $path = sprintf('%s/hasura', $kernel->getProjectDir());
        $remoteSchemasFile = $path . '/remote_schemas.yaml';
        $tester = new CommandTester((new Application($kernel))->find('hasura:metadata:export'));

        $tester->execute(['--force']);
        $this->assertFileExists($remoteSchemasFile);

        $remoteSchemasContentBackup = file_get_contents($remoteSchemasFile);

        (new Filesystem())->remove($remoteSchemasFile);

        $applier = new CommandTester((new Application($kernel))->find('hasura:metadata:apply'));
        $applier->execute([]);

        (new Filesystem())->touch($remoteSchemasFile);

        $tester->execute(['--force']); // re-export
        $this->assertFileDoesNotExist($remoteSchemasFile);

        file_put_contents($remoteSchemasFile, $remoteSchemasContentBackup);

        $applier->execute([]); // restore for next test cases.
    }
}
