<?php

namespace Lib\Tests\Agrirouter {

    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Capability;
    use Agrirouter\Request\Payload\Endpoint\CapabilitySpecification\Direction;
    use Agrirouter\Request\RequestEnvelope;
    use Agrirouter\Request\RequestEnvelope\Mode;
    use Agrirouter\Request\RequestPayloadWrapper;
    use DateTime;
    use Google\Protobuf\Any;
    use Google\Protobuf\Internal\CodedOutputStream;
    use Google\Protobuf\Timestamp;
    use PHPUnit\Framework\TestCase;

    class ProtobufSmokeTest extends TestCase
    {
        /**
         * @covers
         */
        public function testGivenCapabilitiesMessageWhenEncodeMessageThenTheResultShouldBeValid()
        {
            $expected = "RAokNmY5Y2ZiMWEtMGQ5YS00M2JjLWFiMWItMWUwNjliZDE2OGUxEAEaEGRrZTpjYXBhYmlsaXRpZXMoAUIGCICRv9IDgwEKgAEKEGRrZTpjYXBhYmlsaXRpZXMSbAoeChppc286MTE3ODM6LTEwOnRhc2tkYXRhOnppcBABEiRlNjMzMDcwNC0yOGYxLTRhNDYtYTg5YS03ZmZjM2Y4YTBlNDIaJDA3ZDkzYTQ0LTdjMzItNGM4Mi04MTk5LTUwM2QwYTNiODkxMgAAAAAAAAAAAAAAAAAAAAAA";

            $requestEnvelope = $this->encodeHeader();
            $requestPayloadWrapper = $this->encodePayload();

            $f = CodedOutputStream::MAX_VARINT64_BYTES + $requestEnvelope->byteSize() + CodedOutputStream::MAX_VARINT64_BYTES + $requestPayloadWrapper->byteSize();

            $codedOutputStream = new CodedOutputStream($f);
            $codedOutputStream->writeVarint32($requestEnvelope->byteSize(), false);
            $requestEnvelope->serializeToStream($codedOutputStream);
            $codedOutputStream->writeVarint32($requestPayloadWrapper->byteSize(), false);
            $requestPayloadWrapper->serializeToStream($codedOutputStream);

            $rawData = $codedOutputStream->getData();
            $actual = base64_encode($rawData);

            self::assertEquals($expected,$actual);
        }

        private function encodeHeader(): RequestEnvelope
        {
            $requestEnvelope = new RequestEnvelope();
            $requestEnvelope->setApplicationMessageId('6f9cfb1a-0d9a-43bc-ab1b-1e069bd168e1');
            $requestEnvelope->setApplicationMessageSeqNo(1);
            $requestEnvelope->setTechnicalMessageType('dke:capabilities');
            $requestEnvelope->setMode(Mode::PUBLISH);

            $timestamp = new Timestamp();
            $timestamp->fromDateTime(new DateTime('2001-01-01'));
            $requestEnvelope->setTimestamp($timestamp);

            return $requestEnvelope;
        }

        private function encodePayload(): RequestPayloadWrapper
        {
            $requestPayloadWrapper = new RequestPayloadWrapper();
            $any = new Any();
            $any->setTypeUrl("dke:capabilities");
            $any->setValue($this->capabilities());
            $requestPayloadWrapper->setDetails($any);
            return $requestPayloadWrapper;
        }

        private function capabilities(): string
        {
            $capabilitySpecification = new CapabilitySpecification();
            $capabilitySpecification->setAppCertificationId("e6330704-28f1-4a46-a89a-7ffc3f8a0e42");
            $capabilitySpecification->setAppCertificationVersionId("07d93a44-7c32-4c82-8199-503d0a3b8912");

            $capability = new Capability();
            $capability->setTechnicalMessageType('iso:11783:-10:taskdata:zip');
            $capability->setDirection(Direction::RECEIVE);

            $capabilitySpecification->setCapabilities([
                $capability,
            ]);
            return $capabilitySpecification->serializeToString();
        }

    }
}