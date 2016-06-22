<?php

namespace WI\Sitemap\Repositories;

interface SitemapRepositoryInterface {

    //public function getAll();

    /**
     * Fetch a record by id
     *
     * @param $id
     */
    public function getEnabledLocales();

    public function getById($id);


    public function create(array $attributes);
    public function update(array $attributes);
    public function all($columns = array('*'));
    public function find($id, $columns = array('*'));
    public function destroy($ids);

}

