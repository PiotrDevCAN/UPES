/**
 *
 */

function changePesLevels(dataCategory) {
    $("#PES_LEVEL").select2({
        data: dataCategory,
        placeholder: 'Select Pes Level',
        width: '100%'
    })
        .attr('disabled', false)
        .attr('required', true);
}

export { changePesLevels as default };