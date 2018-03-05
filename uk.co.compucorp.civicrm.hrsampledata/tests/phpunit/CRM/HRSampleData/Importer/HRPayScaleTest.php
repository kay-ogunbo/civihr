<?php

/**
 * Class CRM_HRSampleData_Importer_HRPayScaleTest
 *
 * @group headless
 */
class CRM_HRSampleData_Importer_HRPayScaleTest extends CRM_HRSampleData_BaseCSVProcessorTest {

  public function setUp() {
    $this->rows = [];
    $this->rows[] = $this->importHeadersFixture();
  }

  public function testProcess() {
    $this->rows[] = [
      'E2',
      'USD',
      '70000',
      'Year'
    ];

    $this->runProcessor('CRM_HRSampleData_Importer_HRPayScale', $this->rows);

    $payScale = $this->apiGet('HRPayScale', ['pay_scale' => 'E2']);

    $this->assertEntityEqualsToRows($this->rows, $payScale);
  }

  private function importHeadersFixture() {
    return [
      'pay_scale',
      'currency',
      'amount',
      'pay_frequency',
    ];
  }

}
