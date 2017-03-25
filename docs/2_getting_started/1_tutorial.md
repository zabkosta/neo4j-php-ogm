## Getting started with the Neo4j PHP OGM

This quick start guide covers the basics of working with the PHP OGM. At the end you should be able to :

* Install and configure the PHP OGM
* Map PHP objects to Neo4j nodes and relationships
* Use the `EntityManager` to save, load and delete objects in the database

## What is the Neo4j PHP OGM ?

The Neo4j PHP OGM is an **object graph mapper** for PHP5.6+ that provides persistence for PHP objects.
It is heavily inspired by the [Doctrine2](http://www.doctrine-project.org/) project and uses the data mapper pattern 
and dockblock annotations.

## What are entities ?

Entities are PHP objects that can be identified by a unique identifier and represent nodes or relationships in your database.

In contrary to object relational mapper, the Neo4j PHP OGM supports two types of entities : 

* PHP Objects representing **Nodes** in your database
* PHP Objects representing **Relationships** in your datase (named _RelationshipEntity_)

## An example model : The movie database

For this getting started guide, we will implement the Movie Graph model that is available in any Neo4j installation by 
issuing the `:play movies` command in the Neo4j browser.

![Play Movies](_assets/_1_play_movies.png)

