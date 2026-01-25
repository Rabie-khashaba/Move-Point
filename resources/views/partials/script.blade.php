    <!--! ================================================================ !-->
    <!--! Footer Script !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Vendors JS !-->
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <!-- vendors.min.js {always must need to be top} -->
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}"></script>
	
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
	
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>
	
    <script src="{{ asset('assets/vendors/js/select2.min.js') }}"></script>	
    <script src="{{ asset('assets/vendors/js/select2-active.min.js') }}"></script>
	
    <script src="{{ asset('assets/vendors/js/tagify.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/tagify-data.min.js') }}"></script>
	
    <script src="{{ asset('assets/vendors/js/quill.min.js') }}"></script>	
	
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
	
    <script src="{{ asset('assets/vendors/js/jquery.time-to.min.js') }}"></script>
	
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>	

    <script src="{{ asset('assets/vendors/js/tui-code-snippet.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/tui-time-picker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/tui-date-picker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/chance.min.js') }}"></script>

    <script src="{{ asset('assets/vendors/js/time-tracker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/emojionearea.min.js') }}"></script>

	<script src="{{ asset('assets/vendors/js/jquery.time-to.min.js') }}"></script>	
	<script src="{{ asset('assets/vendors/js/jquery.calendar.min.js') }}"></script>

    <script src="{{ asset('assets/vendors/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/dataTables.bs5.min.js') }}"></script>	
	<script src="{{ asset('assets/vendors/js/lslstrength.min.js') }}"></script>
	
	<script src="{{ asset('assets/vendors/js/cleave.min.js') }}"></script>
	
	<script src="{{ asset('assets/vendors/js/jquery.print.min.js') }}"></script>
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>
	
	<script src="{{ asset('assets/js/apps-email-init.min.js') }}"></script>
	
	<script src="{{ asset('assets/js/analytics-init.min.js') }}"></script>	
	
	<script src="{{ asset('assets/js/apps-storage-init.min.js') }}"></script>
	
	<script src="{{ asset('assets/js/apps-tasks-init.min.js') }}"></script>
	
	<script src="{{ asset('assets/js/apps-calendar-init.min.js') }}"></script>
	
	<script src="{{ asset('assets/js/apps-chat-init.min.js') }}"></script>	
	
	<script src="{{ asset('assets/js/settings-init.min.js') }}"></script>	

	<script src="{{ asset('assets/js/widgets-charts-init.min.js') }}"></script>	

	<script src="{{ asset('assets/js/widgets-lists-init.min.js') }}"></script>	

	<script src="{{ asset('assets/js/widgets-miscellaneous-init.min.js') }}"></script>
	
	<script src="{{ asset('assets/js/widgets-statistics-init.min.js') }}"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>

	<script src="{{ asset('assets/js/widgets-tables-init.min.js') }}"></script>

	<script src="{{ asset('assets/js/proposal-create-init.min.js') }}"></script>
	<script src="{{ asset('assets/js/proposal-view-init.min.js') }}"></script>	
	
	<script src="{{ asset('assets/js/reports-leads-init.min.js') }}"></script>
	<script src="{{ asset('assets/js/reports-project-init.min.js') }}"></script>
	<script src="{{ asset('assets/js/reports-sales-init.min.js') }}"></script>
	<script src="{{ asset('assets/js/reports-tmesheets-init.min.js') }}"></script>
	
	<script src="{{ asset('assets/js/leads-view-init.min.js') }}"></script>
	
	<script src="{{ asset('assets/js/leads-init.min.js') }}"></script>
	
	<script src="{{ asset('assets/js/payment-init.min.js') }}"></script>
	
	<script src="{{ asset('assets/js/customers-view-init.min.js') }}"></script>
	
	<script src="{{ asset('assets/js/invoice-create-init.min.js') }}"></script>
	
	<script src="{{ asset('assets/js/invoice-view-init.min.js') }}"></script>
    <!--! END: Theme Customizer !-->
    
    @yield('scripts')