<style>
    .select2-container {
        font-family: "Roboto, sans-serif";
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #ddd;
    }

    .select2-container .select2-selection--single {
        height: 38px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }

    .select2-dropdown {
        z-index: 0 !important;
        border: 1px solid #e0e0e0;
        background: rgb(248, 248, 248);
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #e3e3e3;
        border-radius: 4px;
        padding-left: 7px;
    }

    .select2-results__options {
        padding: 0 4px 4px;
    }

    .select2-results__options li {
        border-radius: 3px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: {{ currentBranch()->color }};
        color: white;
    }

    .select2-results__option.select2-results__message {
        color: #666;
    }

</style>
