@props(['user_info'])
<?php
$bread_crumb['page_main_bread_crumb'] = labels('front_messages.referrals', 'Referrals');

?>

<div>
    <div id="page-content">
        <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />
        <div class="container-fluid">
            <div class="row">
                <x-utility.my_account_slider.account_slider :$user_info />
                <div class="col-12 col-sm-12 col-md-12 col-lg-9">
                    <div class="dashboard-conten h-100">

                        <div class="h-100" id="user-referrals">
                            <div class="account-info h-100">

                                <h2>Your Referrals</h2>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Role</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Members</td>
                                            <td>{{ $referralStats['members'] ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <td>Dealers</td>
                                            <td>{{ $referralStats['dealer'] ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <td>Managers</td>
                                            <td>{{ $referralStats['manager'] ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <td>Sellers</td>
                                            <td>{{ $referralStats['seller'] ?? 0 }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
