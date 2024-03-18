<b-container fluid class="mt-3">
    <b-row>
        <b-col class="mt-3" lg="8">
            Research IT provides professional services to the Stanford community with cost recovery for hands-on
            assistance, software development, and more.
        </b-col>
    </b-row>
    <div v-if="linked() == false">

        <b-row>
            <b-col class="mt-3" lg="8">
                In order to request a support block, please first link this REDCap project to a R2P2 Research Project.
                See the first tab for more details.
            </b-col>
        </b-row>
    </div>
    <div v-else>
        <b-row>
            <b-col class="mt-3" lg="8">
                To easily request professional assistance, a “Support Block” Statement of Work can be created from this
                page. The size of the block and dollar amount should come from documentation or a conversation with RIT
                personnel. All Support Blocks represent an estimate of time and work and do not guarantee project
                completion. Additional Support Blocks may be required.
            </b-col>
        </b-row>
        <b-row>
            <b-col class="mt-3" lg="8">
                After a Support Block is requested, a new Statement of Work (SoW) will be automatically created in R2P2.
                Before work can being, you or someone from the research team must approve the SoW and provide a PTA.
            </b-col>
        </b-row>
        <b-row>
            <b-col class="mt-3" lg="8">
                To learn more about R2P2 and Professional Services view the <a target="_blank"
                                                                               href="https://medwiki.stanford.edu/x/uiK3Cg"><i
                        class="fas fa-external-link-alt"></i> The R2P2 wiki</a>
            </b-col>
        </b-row>
        <b-row :show="showServiceBlockButton" class="mb-3 mt-3">
            <b-col class="mt-3" lg="6">
                <b-button size="sm" variant="success" v-b-modal.service-block-modal>Generate Support Block
                </b-button>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-button @click="getSprintBlocks(true);" size="sm" variant="secondary"
                          v-if="display_historical_sprint_blocks === false">Show Historical Support Blocks
                </b-button>
                <b-button @click="display_historical_sprint_blocks = false" size="sm" variant="secondary"
                          v-if="display_historical_sprint_blocks === true">Hide Historical Support Blocks
                </b-button>
            </b-col>
        </b-row>
        <div v-if="display_historical_sprint_blocks === true">
            <b-row>
                <b-col class="justify-content-center align-self-center mt-3" lg="8">
                    <a :href="ticket.project_portal_sow_url" target="_blank" class="ml-1"><i
                            class="fas fa-external-link-alt"></i>
                        <span>Full list of Project SOWs</span></a>
                </b-col>
            </b-row>
            <b-table class="mt-3" show-empty striped hover bordered v-if="display_historical_sprint_blocks === true"
                     :empty-text="empty_sprint_block"
                     :items="historical_sprint_blocks"
                     :fields="historical_sprint_blocks_fields">
                <template #cell(title)="row">
                    <a target="_blank" :href="ticket.project_portal_sow_url + '/?id=' + row.item.id"><i
                            class="fas fa-external-link-alt"></i>{{row.item.title}}</a>
                </template>
            </b-table>
        </div>
    </div>
</b-container>