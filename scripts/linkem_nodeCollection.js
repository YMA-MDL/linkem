/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/*
 * NodeCollection Class
 */

function nodeCollection() {

    var that = this;

    this.nodes = {};

    this.edges = {};

    this.nodeIds = [];

    this.edgeIds = [];

    this.singleSelection = false;

    this.addNodeToCollection = function (node, isNew) {
        if (isNew) {
            node.createId();
        }
        node.updateTemplateProperties();
        that.nodes[node.id] = node;
        allNodes[node.id] = node;
        that.nodeIds.push(node.id);
    };

    this.selectNode = function (nodeId) {
        // list properties
        propertyList.loadProperties(allNodes[nodeId].properties);
        propertyList.switchPropertyTargetType("Node");
        propertyList.enableActions();
        that.singleSelection = true;
        that.singleSelectionType = "node";
        that.selectedNode = that.nodes[nodeId];
        that.selectedEdge = null;
    };
    this.selectGroupedNodes = function () {
        // list properties
        propertyList.emptyMode();
        propertyList.switchPropertyTargetType("Node");
        propertyList.disableActions();
        propertyList.enableGroupActions();
        that.singleSelection = false;
        that.singleSelectionType = "node";
        that.selectedNode = null;
        that.selectedEdge = null;
    };

    this.selectEdge = function (edgeId) {
        // list properties
        propertyList.loadProperties(allEdges[edgeId].properties);
        propertyList.switchPropertyTargetType("Edge");
        propertyList.enableActions();
        that.singleSelection = true;
        that.singleSelectionType = "edge";
        that.selectedEdge = that.edges[edgeId];
        that.selectedNode = null;
    };

    this.unselectNodes = function () {
        propertyList.emptyMode();
        propertyList.disableActions();
        propertyList.disableGroupActions();
        that.singleSelection = false;
        that.selectedNode = null;
    };

    this.deleteNode = function (node) {
        // in db
        $.post(ajaxUrl, {
            action: "node_delete",
            uniqueId: node.id
        }).success(function (data) {
            console.log(data);
            // delete in collection
            for (var i = 0; i < graphsSetMgr.openGraphList.length; i++) {
                var ag = graphsSetMgr.openGraphList[i];
                ag.nodeCollection.nodeIds.splice(that.nodeIds.indexOf(node.id), 1);
                ag.cy.$("[id='" + node.id + "']").remove();
                delete ag.nodeCollection.nodes[node.id];
                delete allNodes[node.id];
            }
        }).fail(function (err) {
            console.log(err);
        });
        console.log("delete node");
    };

    this.deleteEdge = function (edge) {
        console.log("delete edge");
        // in db
        $.post(ajaxUrl, {
            action: "link_delete",
            uniqueId: edge.id
        }).success(function (data) {
            for (var i = 0; i < graphsSetMgr.openGraphList.length; i++) {
                var ag = graphsSetMgr.openGraphList[i];
                ag.nodeCollection.edgeIds.splice(that.nodeIds.indexOf(edge.id), 1);
                delete  ag.nodeCollection.edges[edge.id];
                delete  allEdges[edge.id];
                ag.cy.$("[id='" + ag.nodeCollection.selectedEdge.id + "']").remove();
            }
        }).fail(function (err) {
            console.log(err);
        });
        console.log("delete node");
    };

    this.detachNode = function (node) {
        var ag = getActiveGraph();
        that.nodeIds.splice(that.nodeIds.indexOf(node.id), 1);
        delete that.nodes[node.id];
        delete allNodes[node.id];
        ag.cy.$("[id='" + ag.nodeCollection.selectedNode.id + "']").remove();
    };

    this.detachEdge = function (edge) {
        var ag = getActiveGraph();
        that.edgeIds.splice(that.nodeIds.indexOf(edge.id), 1);
        delete that.edges[edge.id];
        delete allEdges[edge.id];
        ag.cy.$("[id='" + ag.nodeCollection.selectedEdge.id + "']").remove();
    };

    this.createRelationship = function (source, target, type) {
        var newEdge = new Edge(source, target, type, true);
        allEdges[newEdge.id] = newEdge;
        that.edges[newEdge.id] = newEdge;
        that.edgeIds.push(newEdge.id);
        $.post(ajaxUrl, {
            action: "link_add",
            uniqueIdSource: source,
            uniqueIdTarget: target,
            linkType: type,
            uniqueId: newEdge.id
        }).success(function (data) {
            // when success delete the node from the graph
            console.log(data);
            // add to graph
            var activeGraph = getActiveGraph();
            activeGraph.graphAddEdge(source, target, newEdge.id, type);
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.loadNodeTypeCollection = function (nodeType, graph) {
        $.post(ajaxUrl, {
            action: "nodeTypeQuery",
            nodeType: nodeType
        }).success(function (data) {
            // when success delete the node from the graph
            // add to graph
            graph.displayNodeTypeList(data.nodes, data.edges, nodeType);
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.loadRelationships = function (graph, direction) {
        var nodeId = that.selectedNode.id;
        $.post(ajaxUrl, {
            action: "getRelationships",
            nodeId: nodeId,
            direction: direction
        }).success(function (data) {
            // when success delete the node from the graph
            // add to graph
            if ((data.nodes.length > 0) || (data.edges.length > 1)) {
                graph.displayNodeTypeList(data.nodes, data.edges);
            } else {
                standardNotification("no relationships found", "warning");
            }

        }).fail(function (err) {
            console.log(err);
        });
    };
}
