<!doctype html>
<html ng-app="phidias-specification">
    <head>
        <meta charset="utf-8">

        <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
        <title ng-bind="phidias.title"></title>

        <link href='https://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>

        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js" type="text/javascript"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-aria.min.js" type="text/javascript"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-animate.min.js" type="text/javascript"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-sanitize.min.js" type="text/javascript"></script>

        <style type="text/css">
        html {
            font-family: 'Droid Sans', sans-serif;
            box-sizing: border-box;
        }

        *, *:before, *:after {
            box-sizing: inherit;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        h1, h2, h3, h4 {
            font-weight: normal;
        }

        main {
            display: block;
            margin: 32px 0 0 32px;
            padding: 0;
            max-width: 1024px;
        }

        #search {
        }

        #search input {
            border: 0;
            width: 100%;
            font-size: 1.3em;
            line-height: 1.3em;

            border-bottom: 1px solid #999;
            margin-bottom: 16px;

            outline: none;
        }

        #search input:focus {
            border-bottom: 2px solid #666;
        }

        summary {
            display: block;
            padding: 8px 16px;
            cursor: pointer;

            outline: none;
        }

        summary:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }

        summary::-webkit-details-marker {
            display: none;
        }

        .resource {
            display: block;
            margin: 0;
            padding: 0;
        }

        .resource[open] {
            margin-bottom: 48px;
        }

        .resource[open] > summary {
            background-color: rgba(0,0,0, 0.08);
        }

        .resource summary h2,
        .resource summary p {
            margin: 0;
            padding: 0;

            font-weight: normal;
        }

        .resource summary p {
            color: #655;
        }


        .resource table.attributes {
            width: 100%;

            background-color: rgba(0,0,0, 0.08);
            padding: 8px; /* match with <summary> padding */
            margin: 0;
        }

        .resource table.attributes td {
            vertical-align: top;
        }

        .resource table.attributes td:first-child {
            font-weight: bold;
            width: 120px;
        }

        .resource table.attributes p {
            margin: 0;
            color: #333;
        }

        .resource .exchanges {
            margin: 0;
            background-color: rgba(0,0,0, 0.08);
        }

        .exchange summary {
            padding: 12px;
        }

        .exchange[open] summary {
            background-color: rgba(0,0,0, 0.08);
        }

        .exchange summary strong {

            display: inline-block;
            text-align: center;
            padding: 4px;
            width: 60px;
            margin-right: 1em;

            background-color: #666;
            color: #fff;
            font-weight: normal;
        }

        .exchange.get summary strong {
            background-color: #4dbcd4;
        }

        .exchange.post summary strong {
            background-color: #b6c72b;
        }

        .exchange.put summary strong {
            background-color: #b6c72b;
        }

        .exchange.delete summary strong {
            background-color: #f34541;
        }

        .exchange .request,
        .exchange .response {
            padding: 18px 32px;
            font-family: Courier, sans-serif;
        }

        .exchange p {
            margin: 0;
        }

        .exchange p em {
            font-weight: bold;
            font-style: normal;
        }

        .exchange .body {
            margin-top: 1em;
        }

        .exchange .request {
            background-color: rgba(0,0,0, 0.08);
        }

        .exchange .response {
            padding-bottom: 32px;
            background: #5a615e;
            color: #ddd;
        }

        .resource .resources {
            margin: 8px 0 0 8px;
        }

        </style>


        <script type="text/javascript">



        function Schema() {

            this.expand = function(object) {

                if (object === null) {
                    return object;
                }

                if (typeof object != 'object') {
                    return [object];
                }

                if (object.constructor === Array) {
                    var retval = [];
                    for (var i = 0; i < object.length; i++) {
                        retval = retval.concat(this.expand(object[i]));
                    }
                    return retval;
                }

                // Expand if $any tag is present
                var variations = [];

                if (object.hasOwnProperty('$any')) {

                    for (var k in object['$any']) {

                        var currentOption = object['$any'][k];

                        if (typeof currentOption != 'object') {
                            variations.push(currentOption);
                            continue;
                        }


                        /*
                        Syntactic sugar:
                        Track properties along side "$any" and use them in all variations:

                        turn this

                        {
                            someProperty: someValue,
                            $any: [
                                {
                                    someOtherProperty: someValue
                                },

                                {
                                    someOtherProperty: someOtherValue
                                }
                            ]
                        }

                        into this

                        {
                            $any: [
                                {
                                    someProperty: someValue,
                                    someOtherProperty: someValue
                                },

                                {
                                    someProperty: someValue,
                                    someOtherProperty: someOtherValue
                                }
                            ]
                        }
                        */
                        var mergedObject = {};

                        for (var property in object) {
                            if (property == '$any') {
                                continue;
                            }
                            if (Object.prototype.hasOwnProperty.call(object, property)) {
                                mergedObject[property] = object[property];
                            }
                        }


                        for (var property in currentOption) {
                            if (Object.prototype.hasOwnProperty.call(currentOption, property)) {
                                mergedObject[property] = currentOption[property];
                            }
                        }

                        variations = variations.concat(this.expand(mergedObject));
                    }

                    return variations;
                }



                // Expand object:
                var expanded = {};
                for (var property in object) {
                    if (Object.prototype.hasOwnProperty.call(object, property)) {
                        expanded[property] = this.expand(object[property]);
                    }
                }


                return this.combine(expanded);

            };


            this.combine = function(expansionObject) {

                var retval = [];

                for (var property in expansionObject) {
                    retval = this.addProperty(retval, property, expansionObject[property]);
                }

                return retval;
            };




            /**

            This function takes an array of objects and returns
            a new array containing all possible combinations of the
            given property values:

            ex.
            arr = [
                {
                    "name": "SomeName"
                },

                {
                    "name": "Bar"
                }
            ]

            var result = addProperty(arr, "type", ["a", "b"]);

            result is:

            [
                {
                    "name": "SomeName",
                    "type": "a"
                },

                {
                    "name": "SomeName",
                    "type": "b"
                },

                {
                    "name": "Bar",
                    "type": "a"
                },

                {
                    "name": "Bar",
                    "type": "b"
                }
            ]

            */

            this.addProperty = function(arr, property, values) {

                var incoming = [];

                for (var key in values) {
                    var object       = {};
                    object[property] = values[key];
                    incoming.push(object);
                }

                return this.fuse(arr, incoming);
            };


            this.fuse = function(arrayA, arrayB) {

                if (arrayA.length == 0) {
                    return arrayB;
                }

                var retval = [];

                for (var i in arrayA) {
                    var elementA = arrayA[i];
                    for (var k in arrayB) {
                        var elementB = arrayB[k];

                        retval.push( this.mergeObjects(elementA, elementB) );
                    }
                }

                return retval;

            };


            this.mergeObjects = function(elementA, elementB) {

                // this function returns a new object
                var retval = {};

                // with copies off al properties from elementA
                for (var property in elementA) {
                    if (Object.prototype.hasOwnProperty.call(elementA, property)) {
                        retval[property] = elementA[property];
                    }
                }

                // overwritten with copies of all properties from elementB
                for (var property in elementB) {
                    if (Object.prototype.hasOwnProperty.call(elementB, property)) {
                        retval[property] = elementB[property];
                    }
                }

                return retval;
            };


            this.getExample = function(schema) {

                if (typeof schema !== "object") {
                    return schema;
                }

                if (schema.constructor === Array) {
                    return schema.map(this.getExample);
                }

                if (schema.$type !== undefined) {
                    return this.getTypeExample(schema.$type, schema);
                }

                var retval = {};
                for (property in schema) {
                    retval[property] = this.getExample(schema[property]);
                }
                return retval;

            };

            this.getTypeExample = function(type, schema) {

                switch (type) {

                    case "boolean":
                        return Math.random() < 0.5;
                    break;

                    case "integer":
                        return Math.floor((Math.random() * 999999) + 1);
                    break;

                    case "string":
                        return "A random string";
                    break;

                    case "array":
                        
                        if (schema.$items === undefined) {
                            return ["An array"];
                        }

                        var retval = [];
                        var nItems = Math.floor((Math.random() * 5) + 1);

                        for (var cont = 1; cont <= nItems; cont++) {
                            retval.push(this.getExample(schema.$items));
                        }

                        return retval;

                    break;

                    default:
                        return "An element of type " + type;
                    break;

                }

            };

        };



        function Collection(items) {

            if (typeof items === "object" && items.constructor !== Array) {
                var schema = new Schema();
                items      = schema.expand(items);
            }

            this.items = items || [];

            this.push = function(item) {
                this.items.push(item);
                return this;
            };

            this.getObjectProperty = function(object, property) {

                var currentObject = object;

                var path = property.split(".");
                for (var i = 0; i < path.length; i++) {
                    var currentProperty = path[i];

                    if (!currentObject.hasOwnProperty(currentProperty)) {
                        return undefined;
                    }

                    currentObject = currentObject[currentProperty];
                }


                return currentObject;
            };



            this.distinct = function(property) {

                var retval = new Collection();

                for (var k in this.items) {

                    var item         = this.items[k];
                    var currentValue = this.getObjectProperty(item, property);

                    if (currentValue !== undefined && retval.items.indexOf(currentValue) < 0) {
                        retval.push(currentValue);
                    }

                };

                return retval;

            };


            this.groupBy = function(property) {

                var groups = {};

                for (var k in this.items) {

                    var item  = this.items[k];
                    var value = this.getObjectProperty(item, property);

                    if (!value) {
                        continue;
                    }

                    if (!groups.hasOwnProperty(value)) {
                        groups[value] = new Collection();
                    }

                    groups[value].push(item);

                };

                return groups;

            };



            /*
            collection.match({
                first: "santiago",
                people: {
                    name: "santiago"
                }
            });
            */

            this.match = function(object) {

                var matches = new Collection();

                for (var k in this.items) {

                    var item = this.items[k];

                    if (this.matchesSchema(item, object)) {
                        matches.push(item);
                    }

                };

                return matches;
            }


            this.matchesSchema = function(subject, schema) {

                if (typeof schema !== 'object') {
                    return subject === schema;
                }

                if (typeof subject !== 'object') {
                    return false;
                }

                for (var property in schema) {

                    if (!subject.hasOwnProperty(property)) {
                        return this.matchesSchema(null, schema[property]);
                    }

                    if (!this.matchesSchema(subject[property], schema[property])) {
                        return false;
                    }

                }

                return true;

            }

            this.condense = function() {

                var retval = {};

                for (var k = 0; k < this.items.length; k++) {
                    retval = this.condenseObjects(retval, this.items[k]);
                }

                return retval;

            }

            this.condenseObjects = function(targetObject, sourceObject) {


                for (var property in sourceObject) {

                    var value = sourceObject[property];

                    if (typeof value === 'object') {

                        if ( ! Object.prototype.hasOwnProperty.call(targetObject, property)) {
                            targetObject[property] = {};
                        }

                        targetObject[property] = this.condenseObjects(targetObject[property], value);

                    } else {

                        if ( ! Object.prototype.hasOwnProperty.call(targetObject, property)) {
                            targetObject[property] = [];
                        }

                        targetObject[property].push(value);
                    }

                }


                return targetObject;

            }

        }


        angular.module("phidias-specification",[]);
        angular.module("phidias-specification").controller("mainController", mainController);

        mainController.$inject = ["$scope"];
        function mainController($scope) {

            var vm       = this;
            vm.modules   = <?= json_encode($data) ?>;

            vm.allResources = [];

            for (var i = 0; i < vm.modules.length; i++) {
                pushResource(vm.allResources, vm.modules[i]);
            }
        }

        function pushResource(resourceCollection, resource, parent)
        {
            if (parent !== undefined && parent.url !== undefined) {
                resource.url = parent.url + "/" + resource.url;
            }

            var copy = {};
            for (var property in resource) {
                if (property != 'resources') {
                    copy[property] = resource[property];
                }
            }

            resourceCollection.push(copy);

            if (resource.hasOwnProperty('resources')) {
                for (var i = 0; i < resource.resources.length; i++) {
                    pushResource(resourceCollection, resource.resources[i], resource);
                }
            }
        }

        </script>
    </head>

    <body ng-controller="mainController as vm">
        <main>

            <h1>Documentación</h1>

            <script type="text/ng-template" id="resource.html">
                <details class="resource">
                    <summary>
                        <h2 ng-bind="resource.title"></h2>
                        <p ng-bind="resource.url"></p>
                    </summary>

                    <div>
                        <table class="attributes" ng-if="resource.attributes">
                            <tbody>
                                <tr ng-repeat="(attributeName, attributeData) in resource.attributes">
                                    <td ng-bind="attributeName"></td>
                                    <td ng-bind="attributeData.$type + ' ' + attributeData.$pattern"></td>
                                    <td>
                                        <p ng-bind="attributeData.$title"></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="exchanges" ng-if="resource.exchanges">

                            <details class="exchange {{exchange.request.method}}" ng-repeat="exchange in resource.exchanges">

                                <summary>
                                    <strong ng-bind="exchange.request.method || '!'"></strong>
                                    <span ng-bind="exchange.title || resource.url"></span>
                                </summary>

                                <div class="request">

                                    <p class="url" ng-show="exchange.request.method">
                                        <em ng-bind="exchange.request.method"></em>
                                        <span ng-bind="resource.url"></span>                                    
                                    </p>

                                    <p class="header" ng-repeat="(property, value) in exchange.request.headers">
                                        <em ng-bind="property"></em>:
                                        <span ng-bind="value"></span>
                                    </p>

                                    <div class="body" ng-if="exchange.request.body">
                                        {{exchange.request.body}}
                                    </div>
                                </div>

                                <div class="response">
                                    <p class="code" ng-bind="(exchange.response.code || '200') + ' ' + (exchange.response.reason || 'OK')"></p>

                                    <p class="header" ng-repeat="(property, value) in exchange.response.headers">
                                        <em ng-bind="property"></em>:
                                        <span ng-bind="value"></span>
                                    </p>

                                    <div class="body" ng-if="exchange.response.body">
                                        {{exchange.response.body}}
                                    </div>
                                </div>

                            </details>

                        </div>

                        <div class="resources" ng-if="resource.resources" ng-init="baseUrl = resource.url">
                            <div ng-include="'resource.html'" ng-repeat="resource in resource.resources"></div>
                        </div>

                    </div>
                </details>
            </script>


            <div id="search">
                <input type="text" placeholder="Buscar ..." ng-model="search" />

                <div class="results" ng-if="!!search.length">
                    <div ng-repeat="resource in vm.allResources|filter:search">
                        <div ng-include="'resource.html'"></div>
                    </div>
                </div>
            </div>

            <div ng-repeat="module in vm.modules" ng-show="!search">

                <details class="resource module">
                    <summary>
                        <h2 ng-bind="module.title"></h2>
                        <p ng-bind="module.description"></p>
                    </summary>

                    <div>
                        <div class="resources" ng-if="module.resources">
                            <div ng-include="'resource.html'" ng-repeat="resource in module.resources"></div>
                        </div>
                    </div>
                </details>

            </div>

        </main>
    </body>

</html>