<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */

?>
<div class="row">
    <?php
    echo $module->getSystemSetting('rit-dashboard-external-modules-tab-header');
    ?>
</div>
<b-row>
    <b-col lg="6" class="my-1">
        <b-form-group
                label="Filter"
                label-for="filter-input"
                label-cols-sm="3"
                label-align-sm="right"
                label-size="sm"
                class="mb-0"
        >
            <b-input-group size="sm">
                <b-form-input
                        id="filter-input"
                        v-model="filter_em"
                        type="search"
                        placeholder="Type to Search"
                ></b-form-input>

                <b-input-group-append>
                    <b-button :disabled="!filter_em" @click="filter_em = ''">Clear</b-button>
                </b-input-group-append>
            </b-input-group>
        </b-form-group>
    </b-col>
    <b-col sm="7" md="6" class="my-1">
        <b-pagination
                v-model="currentPage_em"
                :total-rows="totalRows_em"
                :per-page="perPage_em"
                align="fill"
                size="sm"
                class="my-0"
        ></b-pagination>
    </b-col>
</b-row>
<b-table striped hover :items="items_em" :fields="fields_em" :current-page="currentPage_em"
         :per-page="perPage_em"
         :filter="filter_em" @filtered="onFilteredEM">
    <!--    <template #cell(id)="data">-->
    <!--        <span v-html="data.value"></span>-->
    <!--    </template>-->
</b-table>