<b-container fluid class="mt-3">
    <b-row>
        <b-col lg="12">
            Research IT provides professional services to the Stanford community with cost recovery for hands-on
            assistance, software development, and more. To learn about our offerings, check out our <a target="_blank"
                                                                                                       href="https://medwiki.stanford.edu/x/uiK3Cg">the
                R2P2 Wiki</a>
        </b-col>
    </b-row>
    <div v-if="linked() == false">

        <b-row>
            <b-col lg="12">
                In order to request a sprint block, please first link this REDCap project to a R2P2 Research Project.
                See the first tab for more details.
            </b-col>
        </b-row>
    </div>
    <div v-else>
        <b-row>
            <b-col lg="12">
                To easily request professional assistance, a “Sprint Block” Statement of Work can be created from this
                page. Simply select the level, enter a description of the work to be performed, and submit. A new
                Statement of Work will be automatically created in R2P2. You MUST first approve the SoW and provide a
                PTA before work will be initiated.
            </b-col>
        </b-row>
        <b-row>
            <b-col lg="12">
                * Please note that a Sprint Block is an estimate of work. Work not completed in the allotted time may
                require another Sprint Block. See our <a target="_blank" href="https://medwiki.stanford.edu/x/uiK3Cg">wiki</a>
                for more details
            </b-col>
        </b-row>
        <b-row :show="showServiceBlockButton" class="mb-3 mt-3">
            <b-col lg="6">
                <b-button size="sm" variant="success" v-b-modal.service-block-modal>Generate Service Block
                </b-button>
            </b-col>
        </b-row>
        <b-row>

            <b-col class="justify-content-center align-self-center" lg="12">
                <a :href="ticket.project_portal_sow_url" target="_blank" class="ml-1"><i
                            class="fas fa-external-link-alt"></i>
                    <span>{{ticket.project_portal_name}} Statements of Work</span></a>
            </b-col>

        </b-row>
    </div>
</b-container>